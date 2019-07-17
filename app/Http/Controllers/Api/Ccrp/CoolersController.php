<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\Setting\CoolerAddRequest;
use App\Http\Requests\Api\Ccrp\Setting\CoolerStatusRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Product;
use App\Models\Ccrp\Reports\CoolerLog;
use App\Models\Ccrp\Sys\CoolerType;
use App\Transformers\Ccrp\CoolerHistoryTransformer;
use App\Transformers\Ccrp\CoolerTransformer;
use App\Transformers\Ccrp\CoolerType100Transformer;
use App\Transformers\Ccrp\Sys\CoolerTypeTransformer;
use Illuminate\Http\Request;

;

class CoolersController extends Controller
{
    private $cooler;

    public function __construct(Cooler $cooler)
    {

        $this->cooler = $cooler;
    }

    public function index()
    {
        $this->check();
        $status=request()->get('status')??1;
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('status', $status);
        if (request()->get('has_collector')) {
            $coolers = $coolers->where('collector_num', '>', 0);
        }
        if ($keyword=request()->get('keyword'))
        {
            $coolers = $coolers->where('cooler_name','like','%'.$keyword.'%')->whereOr('cooler_sn','like','%'.$keyword.'%');
        }
        $coolers = $coolers->with('company')
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->paginate(request()->get('pagesize')??$this->pagesize);
        return $this->response->paginator($coolers, new CoolerTransformer());
    }

    public function all()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->get();
        return $this->response->collection($coolers, new CoolerTransformer());
    }

    public function show($cooler)
    {
        $this->check();
        $cooler = $this->cooler->whereIn('company_id', $this->company_ids)->find($cooler);
        if ($cooler) {
            return $this->response->item($cooler, new CoolerTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function history($cooler)
    {
        $this->check();
        $start = request()->start ?? date('Y-m-d H:i:s', time() - 4 * 3600);
        $end = request()->end ?? date('Y-m-d 23:59:59', strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        $cooler = $this->cooler->whereIn('company_id', $this->company_ids)->with('collectors')->find($cooler);
        if ($cooler) {
            $data = $cooler->history($start_time, $end_time);
            return $this->response->item($data, new CoolerHistoryTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function coolerType100()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('cooler_type', 100);
        $coolers = $coolers->with(['category', 'collectors'])
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->paginate($this->pagesize);
        return $this->response->paginator($coolers, new CoolerType100Transformer());
    }

    public function store(CoolerAddRequest $request)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $request['company_id'] = $this->company->id;
        $request['install_uid'] = $this->user->id;
        $result = $this->cooler->addCooler($request->all());
        return $this->response->item($result, new CoolerTransformer)->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $cooler = $this->cooler->find($id);
        $cooler->fill($request->all());
        $cooler->save();
        $cooler->warningSetting()->update(['category_id'=>$request->category_id]);
        return $this->response->item($cooler, new CoolerTransformer());
    }
    //备用、维修、启用 关闭探头，关闭报警
    public function coolerStatus(CoolerStatusRequest $request, $id)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);

        $cooler = $this->cooler->find($id);
        $status = $request->status;
        $offline_check=$status==1?1:0;
        $temp_warning=$status==1?1:0;

        if ($cooler) {
            $post['cooler_id'] = $cooler['cooler_id'];
            $post['cooler_sn'] = $cooler['cooler_sn'];
            $post['cooler_name'] = $cooler['cooler_name'];
            $post['category_id'] = $cooler['category_id'];
            $post['company_id'] = $cooler['company_id'];
            $post['status'] = $status;
            $post['note'] = $request->get('note');
            $post['note_time'] = time();
            $post['note_uid'] = $this->user->id;
            //添加操作日志
            $logmodel = CoolerLog::create($post);
            $set['status'] = $status;

            if ($logmodel) {
                if ($status == Cooler::状态_报废) {
                    $set['uninstall_time'] = time();
                    $cooler->update($set);
                    if ($cooler['collector_num'] > 0) {
                        foreach ($cooler->collectors as $vo) {
                            (new Collector)->uninstall($vo['collector_id'], '冷链装备报废');
                        }
                    }
                } else {
                    $cooler->update($set);
                    $cooler->collectors()->update(['offline_check' => $offline_check]);
                    if ($cooler['collector_num'] > 0) {
                        foreach ($cooler->collectors as $vo) {
                            $vo->warningSetting()->update(['temp_warning' => $temp_warning]);

                        }
                    }
                }

                return $this->response->item($cooler, new CoolerType100Transformer());
            } else {
                return $this->response->errorInternal('操作失败');
            }
        }
    }
    public function coolerType()
    {
        return $this->response->collection(CoolerType::all(), new CoolerTypeTransformer());
    }
    public function getCoolerStatus()
    {
        $result=[];
        foreach (Cooler::$status as $key=>$status)
        {
            $result[]=[
                'value'=>$key,
                'label'=>$status,
            ];
        }
        return $this->response->array(Cooler::$status);
    }

}
