<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\DeliverOrderRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Deliver;
use App\Models\Ccrp\DeliverOrder;
use App\Models\Ccrp\DeliverVehicle;
use App\Models\Ccrp\DeliverWarningSetting;
use App\Models\Ccrp\DeliverOrderLog;
use App\Transformers\Ccrp\DeliverOrderTransformer;
use function App\Utils\creat_deliverorder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliverOrdersController extends Controller
{
    private $model;

    public function __construct(DeliverOrder $deliverOrder)
    {
        $this->model = $deliverOrder;
    }

    public function index()
    {
        $this->check();
        $status = request()->get('status', 0);
        $deliverorder = $this->model->whereIn('company_id', $this->company_ids)->where('status', $status);
        if ($keyword = request()->get('keyword')) {
            $deliverorder = $deliverorder->where('deliverorder', 'like', '%'.$keyword.'%')->whereOr('customer_name', 'like', '%'.$keyword.'%');
        }
        $deliverorder = $deliverorder->orderBy('deliverorder_id', 'desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($deliverorder, new DeliverOrderTransformer());
    }

    public function show($id)
    {
        $this->check();
        if (is_numeric($id)) {
            $deliver_order = $this->model->find($id);
        } else {
            $deliver_order = new DeliverOrder();
        }
        $used = $this->model->where('finished', 0)->whereIn('company_id', $this->company_ids)->select('collector_id')->get();
        $collectors = Collector::query()->whereHas('cooler', function ($query) {
            $query->whereIn('cooler_type', [Cooler::设备类型_保温箱, Cooler::设备类型_冷藏车]);
        })->whereIn('company_id', $this->company_ids)->whereIn('status', [0, 1])->select('collector_id', 'collector_name')->get();
        $used_collector = [];
        foreach ($used as $vvo) {
            $used_collector[] = $vvo['collector_id'];
        }
        foreach ($collectors as &$vo) {
            $vo['is_used'] = 0;
            if (in_array($vo['collector_id'], $used_collector))
                $vo['collector_name'] = '[在途] '.$vo['collector_name'];
                $vo['is_used'] = 1;
        }
        $deliverorder = creat_deliverorder($this->company->id);
        //常用车辆
        $delivervehicle = DeliverVehicle::query()->whereIn('company_id', $this->company_ids)->where('status',1)->orderBy('vehicle','asc')->get(['delivervehicle_id','vehicle']);
        //常用派件人
        $deliver = Deliver::query()->whereIn('company_id', $this->company_ids)->where('status',1)->orderBy('deliver','asc')->get(['deliver_id','deliver','phone']);

        //报警设置
        $deliverorder_warning_setting =DeliverWarningSetting::query()->whereIn('company_id', $this->company_ids)->where('status',1)->get(['id','setting_name']);
        $time=time() - 3600 * 24 * 90;
        //常送客户
        $deliverorder_customer_name = $this->model->selectRaw('customer_name')->whereRaw("customer_name <> '' and company_id in (".implode(',',$this->company_ids).") and length(customer_name)>2 and finished_time>".$time)->groupBy('customer_name')->get();
        return $this->response->item($deliver_order, new DeliverOrderTransformer())
            ->addMeta('deliverorder', $deliverorder)
            ->addMeta('delivervehicle', $delivervehicle)
            ->addMeta('deliverorder_warning_setting', $deliverorder_warning_setting)
            ->addMeta('deliverorder_customer_name', $deliverorder_customer_name)
            ->addMeta('deliver', $deliver)
            ->addMeta('collectors', $collectors);
    }

    public function update($id)
    {
        $this->check();
        $deliverorder = $this->model->find($id);
        $this->authorize('unit_operate', $deliverorder->company);
        $request = request()->all();
        $result = $deliverorder->update($request);
        if ($result) {
            return $this->response->item($deliverorder, new DeliverOrderTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(DeliverOrderRequest $request)
    {
        $this->check();
        $request['create_uid'] = $this->user->id;
        $request['create_time'] =Carbon::now()->addMinutes($request->get('create_time_last',0))->timestamp;
        $request['company_id'] = $this->company->id;
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new DeliverOrderTransformer());
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

    public function finished($id,Request $request)
    {
        $this->check();
        $deliverorder = $this->model->find($id);
        $this->authorize('unit_operate', $deliverorder->company);
        $request['finished_time'] = time();
        $request['finished'] = 1;
        if($request['suborder']==1){
            $suborder = 1+$this->model->where('deliverorder_main',$deliverorder['deliverorder'])->count();
            unset($deliverorder['deliverorder_id']);
            $deliverorder['finished_time']  = $request['finished_time'];
            $deliverorder['finished']  = $request['finished'];
            $deliverorder['suborder']  = $suborder;
            $deliverorder['deliverorder_main']  = $deliverorder['deliverorder'];
            $deliverorder['deliverorder']  = $deliverorder['deliverorder'].'-'.$suborder;
            $deliverorder['customer_name']  = $request['customer_name'];
            $deliverorder['finished_note']  = $request['finished_note'];
            $result=$this->model->create($deliverorder->toArray());
            return $this->response->item($result,new DeliverOrderTransformer());
        }
        $result = $this->model->update($request->all());
        if ($result)
        {
            //关闭报警
            if ($deliverorder->warningSetting)
            {
                $deliverorder->warningSetting()->update(['temp_warning'=>0]);
            }
            return $this->response->item($deliverorder,new DeliverOrderTransformer());
        } else {
            return $this->response->errorInternal('操作失败');
        }

    }


}
