<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CollectorRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Collectorguanxi;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\CompanyHasSetting;
use App\Models\Ccrp\CompanyUseSetting;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\DataHistory;
use App\Models\Ccrp\Dccharging;
use App\Models\Ccrp\Product;
use App\Models\Ccrp\Sender;
use App\Models\Ccrp\Sys\Setting;
use App\Models\Ccrp\Sys\SysCoolerType;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\CollectorDetailTransformer;
use App\Transformers\Ccrp\CollectorHistoryTransformer;
use App\Transformers\Ccrp\CollectorRealtimeTransformer;
use App\Transformers\Ccrp\Sys\CoolerTypeTransformer;
use function App\Utils\abs2;
use function App\Utils\http;
use function App\Utils\to_dianliang;
use function App\Utils\to_dianya;
use function App\Utils\to_rssi;
use function App\Utils\to_shidu;
use function App\Utils\to_wendu;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{
    use ControllerDataRange;
    public $default_date = '今日';
    private $collector;

    public function __construct(Collector $collector)
    {

        $this->collector = $collector;
    }

    public function index()
    {
        $this->check();
        $status = request()->get('status') ?? 1;
        $collectors = $this->collector->whereIn('company_id', $this->company_ids)->where('status', $status);
        if ($keyword = request()->get('keyword')) {
            $collectors = $collectors->where(function ($query) use ($keyword){
                $query->where('collector_name', 'like', '%'.$keyword.'%')->orWhere('supplier_collector_id', 'like', '%'.$keyword.'%');
            });
        }
        if (request()->get('volt_worry') == 1) {
            $collectors = $collectors->whereRaw('(((volt < '.$this->collector::COLLECTOR_WORRY_VOLT['ZKS_S1_COOL'].') and (supplier_product_model="LWTG310") and temp >=-10  ) OR ((volt < '.$this->collector::COLLECTOR_WORRY_VOLT['ZKS_S1_COLD'].') and (supplier_product_model="LWTG310") and temp <-10  ) OR ((volt < '.$this->collector::COLLECTOR_WORRY_VOLT['ZKS_S2'].') and (supplier_product_model="LWTGD310") and temp <-10  ))');
        }
        $collectors = $collectors->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate(request()->get('pagesize') ?? $this->pagesize);

        return $this->response->paginator($collectors, new CollectorDetailTransformer());
    }


    public function show($collector)
    {
        $this->check();
        $collector = $this->collector->find($collector);
        if ($collector) {
            return $this->response->item($collector, new CollectorDetailTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function history($collector)
    {

        $this->set_default_datas($this->default_date);
        $this->check();
        if (request()->date_range) {
            $dates = $this->get_dates();
            $start_time = $dates['date_start'];
            $end_time = $dates['date_end'];
        } elseif (request()->start and request()->end) {
            $start = request()->start ?? date('Y-m-d H:i:s', time() - 4 * 3600);
            $end = request()->end ?? date('Y-m-d 23:59:59', strtotime($start));
            $start_time = strtotime($start);
            $end_time = strtotime($end);
        } else {
            $dates = $this->get_dates();
            $start_time = $dates['date_start'];
            $end_time = $dates['date_end'];
        }
        $collector = $this->collector->whereIn('company_id', $this->company_ids)->where('collector_id', $collector)->first();
        if ($collector) {
            $data = $collector->history($start_time, $end_time);
            return $this->response->collection($data, new CollectorHistoryTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function realtime()
    {
        $this->check();
        $collectors = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc');
        if ($type = request()->get('type') and $type != '' and $type != 'all') {
            if ($type == 'overtemp') {
                $collectors = $collectors->whereIn('warning_type', [$this->collector::预警状态_高温, $this->collector::预警状态_低温]);
            } elseif ($type == 'offline') {
                $collectors = $collectors->where('warning_status', $this->collector::预警类型_离线);
            }
        }
        $count['all'] = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->count();
        $count['offline'] = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->where('warning_status', $this->collector::预警类型_离线)->count();
        $count['overtemp'] = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->whereIn('warning_type', [$this->collector::预警状态_高温, $this->collector::预警状态_低温])->count();
        return $this->response->paginator($collectors->paginate($this->pagesize), new CollectorRealtimeTransformer())->addMeta('count', $count);
    }

    public function store(CollectorRequest $request)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $request['company_id'] = $this->company->id;
        $request['install_uid'] = $this->user->id;
        $cooler = Cooler::where('cooler_id', $request->cooler_id)->first();
        if ($cooler->status == Cooler::状态_报废) {
            return $this->response->errorBadRequest('该冰箱已报废');
        }
        if ($cooler->status == Cooler::状态_维修) return $this->response->errorBadRequest('冷链装备正在维修中');
        if ($cooler->status == Cooler::状态_备用) return $this->response->errorBadRequest('冷链装备是备用状态，请先启用');
        $request['category_id'] = $cooler['category_id'];
        $request['cooler_name'] = $cooler['cooler_name'];
        $product = Product::where('supplier_product_model', $request->supplier_product_model)->first();
        $request['supplier_id'] = $product['supplier_id'];
        if ($request['supplier_product_model'] == 'LWYL201') {
            $request['offline_check'] = 0;
        }
        if ($offline_span = $this->company->hasSettings()->where('setting_id', Company::单位设置_离线报警时长)->first()) {
            $request['offline_span'] = $offline_span->value;

        } elseif ($offline_span = CompanyHasSetting::query()->where('setting_id', Company::单位设置_离线报警时长)->whereIn('company_id', $this->company->getManagerId())->first()) {
            $request['offline_span'] = $offline_span->value;

        } else {
            $offline_span = Setting::find(Company::单位设置_离线报警时长);
            $request['offline_span'] = $offline_span->value;

        }
        $result = $this->collector->create($request->all());
        if ($result) {
            $result->cooler->collector_num++;
            $result->cooler->save();
            (new Collectorguanxi())->addnew($request['supplier_collector_id'], $request['supplier_id']);//供应商ID
        }
        return $this->response->item($result, new CollectorDetailTransformer())->setStatusCode(201);

    }

    public function update(Request $request, $id)
    {
        $this->check();
        $cooler_object = new Cooler();
        $old = $this->collector->find($id);
        $this->authorize('unit_operate', $old->company);
        $request['install_uid'] = $this->user->id;
        //更换了冰箱
        if ($request->get('cooler_id') && ($request->get('cooler_id') <> $old['cooler_id'])) {
            unset($request['collector_id']);
            $request['supplier_collector_id'] = $old['supplier_collector_id'];
            $request['company_id'] = $this->company->id;
            $request['supplier_product_model'] = $old['supplier_product_model'];
            $request['supplier_id'] = $old['supplier_id'];
            $cooler = $cooler_object->find($request->get('cooler_id'));
            $request['category_id'] = $cooler['category_id'];
            $request['cooler_name'] = $cooler['cooler_name'];
            $this->collector->uninstall($old['collector_id'], '更换监测装备');
            $result = $this->collector->create($request->all());
            if ($result)
                $cooler_object->flush_collector_num($request->get('cooler_id'));
            return $this->response->item($result, new CollectorDetailTransformer());

        } else {
            $result = $old->update($request->all());
        }
        if ($result) {
            $cooler_object->flush_collector_num($old['cooler_id']);
            return $this->response->item($old, new CollectorDetailTransformer());

        } else {
            return $this->response->errorInternal('修改失败');
        }

    }

    public function uninstall($id)
    {
        $this->check();
        $change_note = request()->get('change_note');
        if ($change_note == '') return $this->response->errorBadRequest('备注不能为空');
        if (strlen($change_note) < 4) return $this->response->errorBadRequest('“备注”中请填写清楚具体报废的原因');
        $collector = $this->collector->find($id);
        $this->authorize('unit_operate', $collector->company);
        if ($collector->status == Collector::状态_报废) {
            return $this->response->errorMethodNotAllowed('该探头已经报废');
        }
        $result = $this->collector->uninstall($id, $change_note);
        if ($result) {
            return $this->response->noContent();
        } else {
            return $this->response->errorInternal('报废失败');
        }
    }

    public function coolerType()
    {
        return $this->response->collection(SysCoolerType::all(), new CoolerTypeTransformer());
    }

    public function countWarningSettingUnset()
    {
        $this->check();
        $count = $this->collector->whereDoesntHave('warningSetting', function ($query) {
            $query->where('temp_warning', 1)->where('status', 1);
        })->where('status', Collector::状态_正常)->whereIn('company_id', $this->company_ids)->get();
        $warning_count=0;
        $status_3=0;//Cooler::状态_备用
        $status_6=0;//Cooler::状态_除霜
        $status_5=0;//Cooler::状态_盘苗
        if (!$count->isEmpty())
        {
            $coolerIds=collect($count->pluck('cooler_id'))->unique()->values()->all();
            $coolers=Cooler::query()->whereIn('cooler_id',$coolerIds)->get();
            foreach ($coolers as $cooler)
            {
                if ($cooler->status==Cooler::状态_备用)
                {
                    $status_3++;
                }
                if ($cooler->status==Cooler::状态_除霜)
                {
                    $status_6++;
                }
                if ($cooler->status==Cooler::状态_盘苗)
                {
                    $status_5++;
                } 
            }
            foreach ($count as $item)
            {
                if ($item->cooler->status==1)
                {
                    $warning_count++;
                }
               
            }
        }
        return $this->response->array(['warning_count' => $warning_count,'status_3'=>$status_3,'status_6'=>$status_6,'status_5'=>$status_5]);

    }

    public function couveuse()
    {
        $this->check();
        $collectors = $this->collector->whereIn('status', [0, 1])->where('supplier_product_model', 'LWYL201')->whereIn('company_id', $this->company_ids);
        if ($keyword = request()->get('keyword')) {
            $collectors = $collectors->where(function ($query) use ($keyword){
                $query->where('collector_name', 'like', '%'.$keyword.'%')->orWhere('supplier_collector_id', 'like', '%'.$keyword.'%');
            });
        }
        $collectors = $collectors
           ->paginate(request()->get('pagesize') ?? $this->pagesize);
        $sender=new Sender;
        foreach($collectors as &$collector){
            $DC =$sender->getRealTimeStatus($collector['supplier_collector_id'],$collector['supplier_id']);
            $collector['power'] = $DC[0]['ram_count'];
            $collector['power'] = to_dianliang($collector['power']);
            $collector['temp'] = to_wendu($collector['temp']+$collector['temp_fix']);//temp_fix
            $collector['humi'] = to_shidu($collector['humi']);
            $collector['rssi'] = to_rssi($collector['rssi']);
            $collector['volt'] = to_dianya($collector['volt']);
        }
        return $this->response->paginator($collectors, new CollectorDetailTransformer());

    }

    public function couveuse_current($id)
    {

        $object = $this->collector;
        $info = $object->find($id);
        $location = $this->get_jizhan($info['supplier_collector_id']);
        if ($location) {
            if ($location['status'] == 0) {
                $ddata['map_time'] = $info['refresh_time'];
                $ddata['map_lat'] = $location['lat'];
                $ddata['map_lon'] = $location['lon'];
                if ($location['lat'] and $location['lon'] and !$location['address']) {
                    $address = $this->get_baidu_address($location['lat'], $location['lon']);
                    if ($address['status'] == 0) {
                        $ddata['map_address'] = $address['result']['formatted_address'];
                    }
                } else {
                    $ddata['map_address'] = $location['address'];
                }
                $info->update($ddata);
                return $this->response->item($info, new CollectorDetailTransformer());
            }
        } else {
            return $this->response->errorInternal('获取位置失败');
        }
    }

    public function couveuse_refresh($id)
    {

        $object = $this->collector;
        $info = $object->find($id);
        $location = $this->get_jizhan($info['supplier_collector_id']);

        if ($location) {
            if ($location['status'] == 0) {
                $ddata['map_time'] = $info['refresh_time'];
                $ddata['map_lat'] = $location['lat'];
                $ddata['map_lon'] = $location['lon'];

                if ($location['lat'] and $location['lon'] and !$location['address']) {
                    $address = $this->get_baidu_address($location['lat'], $location['lon']);
                    if ($address['status'] == 0) {
                        $ddata['map_address'] = $address['result']['formatted_address'];

                    }
                } else {
                    $ddata['map_address'] = $location['address'];
                }
                $info->update($ddata);
                return $this->response->item($info, new CollectorDetailTransformer());
            } else {
                return $this->response->errorInternal($location['cause']);
            }
        } else {
            return $this->response->errorInternal('获取位置失败');
        }
    }

    public function get_jizhan($sender_id, $map = array(), $getaddress = 0)
    {
        $url = 'http://dd.coldyun.com/lbs/multi';
        $rs_obj = array();
        $collector = $this->collector->where(array('supplier_collector_id' => $sender_id, 'status' => 1))->first();
        $sensor_id = abs2($collector['supplier_id']).'_'.$collector['supplier_collector_id'];
        $table = "senderlbs.".$sensor_id;
        $model = new DataHistory();
        $model->setTable($table);
        $data = $model->where(['isadd' => 0], ['lac1', '<>', 0])->first();
        $httparr = array('sensor' => $sender_id, 'time' => $data['sender_trans_time']);
        $http = http('GET', $url, $httparr);
        $rs = json_decode($http, true);
        $arr = $rs[0];
        $arr['status'] = 0;
        return $arr;

    }

    public function get_baidu_address($lat, $lon)
    {
        $key = env('BAIDU_MAP_API_KEY_SERVER');
        $url = 'http://api.map.baidu.com/geocoder/v2/?ak='.$key.'&location='.$lat.','.$lon.'&output=json&pois=1';
        $httpstr = http('GET', $url);
        $rs_obj = json_decode($httpstr, true);
        return $rs_obj;
    }
}
