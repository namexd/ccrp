<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CollectorRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Collectorguanxi;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Product;
use App\Models\Ccrp\Sys\CoolerType;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\CollectorDetailTransformer;
use App\Transformers\Ccrp\CollectorHistoryTransformer;
use App\Transformers\Ccrp\CollectorRealtimeTransformer;
use App\Transformers\Ccrp\Sys\CoolerTypeTransformer;
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
        $status=request()->get('status')??1;
        $collectors = $this->collector->whereIn('company_id', $this->company_ids)->where('status', $status)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate($this->pagesize);

        return $this->response->paginator($collectors, new CollectorDetailTransformer());
    }



    public function show($collector)
    {
        $this->check();
        $collector = $this->collector->whereIn('company_id',$this->company_ids)->find($collector);
        if($collector)
        {
            return $this->response->item($collector, new CollectorDetailTransformer());
        }else{
            return $this->response->noContent();
        }
    }

    public function history($collector)
    {

        $this->set_default_datas($this->default_date);
        $this->check();
        if(request()->date_range)
        {
            $dates = $this->get_dates();
            $start_time = $dates['date_start'];
            $end_time = $dates['date_end'];
        }elseif(request()->start and request()->end){
            $start = request()->start ?? date('Y-m-d H:i:s',time()-4*3600);
            $end = request()->end ?? date('Y-m-d 23:59:59',strtotime($start));
            $start_time = strtotime($start);
            $end_time = strtotime($end);
        }else{
            $dates = $this->get_dates();
            $start_time = $dates['date_start'];
            $end_time = $dates['date_end'];
        }
        $collector = $this->collector->whereIn('company_id',$this->company_ids)->where('collector_id',$collector)->first();
        if($collector)
        {
            $data = $collector->history($start_time,$end_time);
            return $this->response->collection($data, new CollectorHistoryTransformer());
        }else{
            return $this->response->noContent();
        }
    }

    public function realtime()
    {
        $this->check();
        $collectors = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc');
        if($type = request()->get('type') and $type !='' and $type != 'all')
        {
            if($type =='overtemp')
            {
                $collectors = $collectors->whereIn('warning_type',[$this->collector->预警状态_高温,$this->collector->预警状态_低温]);
            }elseif($type =='offline')
            {
                $collectors= $collectors->where('warning_status',$this->collector->预警类型_离线);
            }
        }
        $count['all'] = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->count();
        $count['offline'] = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->where('warning_status',$this->collector->预警类型_离线)->count();
        $count['overtemp'] = $this->collector->whereIn('company_id', $this->company_ids)->where('status', 1)->whereIn('warning_type',[$this->collector->预警状态_高温,$this->collector->预警状态_低温])->count();
        return $this->response->paginator($collectors->paginate($this->pagesize), new CollectorRealtimeTransformer())->addMeta('count',$count);
    }
    public function store(CollectorRequest $request)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $request['company_id']=$this->company->id;
        $request['install_uid']=$this->user->id;
        $cooler = Cooler::where('cooler_id',$request->cooler_id)->first();
        if ($cooler->status==Cooler::状态_报废)
        {
            return $this->response->errorBadRequest('该冰箱已报废');
        }
        $request['category_id'] = $cooler['category_id'];
        $request['cooler_name'] = $cooler['cooler_name'];
        $product =Product::where('supplier_product_model',$request->supplier_product_model)->first();
        $request['supplier_id'] = $product['supplier_id'];
        if ($request['supplier_product_model'] == 'LWYL201') {
            $request['offline_check'] = 0;
        }
            $result = $this->collector->create($request->all());
            if ($result)
            {
                $result->cooler->collector_num++;
                $result->cooler->save();

                $aa= (new Collectorguanxi())->addnew($request['supplier_collector_id'], $request['supplier_id']);//供应商ID
            }
        return $this->response->item($result, new CollectorDetailTransformer())->setStatusCode(201);

    }

    public function update(Request $request, $id)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $collector=$this->collector->find($id);
        $collector->fill($request->all());
        $collector->save();
        return $this->response->item($collector,new CollectorDetailTransformer());
    }

    public function coolerType()
    {
        return $this->response->collection(CoolerType::all(),new CoolerTypeTransformer());
    }
}
