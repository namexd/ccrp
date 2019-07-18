<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Events\AutoHandleApply;
use App\Models\App;
use App\Models\Ccrp\EquipmentChangeApply;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\EquipmentChangeApplyTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EquipmentChangeApplyController extends Controller
{
    use ControllerDataRange;
    protected $model;
    public $default_date='全部';
    public function __construct(EquipmentChangeApply $equipmentChangeApply)
    {
        $this->model = $equipmentChangeApply;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->check();
        $company_ids = $this->company_ids;
        if ( $request->has('status')) {
            $status=$request->status;
            if (!is_array($status)) {
                $status = [$status];
            }
            $this->model = $this->model->whereIn('status', $status);
        }
        if ($request->has('change_type')) {
        $change_type=$request->change_type;
            $this->model = $this->model->whereHas('details',function ($query) use ($change_type){
               $query->where('change_type',$change_type);
            });
        }
        $this->set_default_datas($this->default_date);
        $this->model = $this->model->whereBetween('apply_time', $this->get_dates('datetime'));
        $data = $this->model->with(['company', 'details', 'news'])->whereIn('company_id', $company_ids)->orderBy('id', 'desc')->paginate($request->pagesize ?? $this->pagesize);
        return $this->response->paginator($data, new EquipmentChangeApplyTransformer())->addMeta('date_range', $this->get_dates('datetime', true));;
    }

    public function getChangeType()
    {
        $this->check();
        return $this->response->array(['data' => $this->model->getChangeType()]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check();
        if ($this->company->hasUseSettings(16, 1)) {
            $request['status'] = EquipmentChangeApply::状态_待审核;
        } else {
            $request['status'] = EquipmentChangeApply::状态_未处理;
        }
        $result = $this->model->add($request->all());
        if ($result instanceof Model) {
            if ($result->status == EquipmentChangeApply::状态_未处理) {
                event(new AutoHandleApply($result));
            }
            return $this->response->item($result, new EquipmentChangeApplyTransformer())->statusCode(201);
        } else
            $this->response->errorInternal('提交失败');
    }

    public function checkApply($id, $status)
    {
        $this->check();
        if ($this->company->cdc_admin == 0) {
            return $this->response->errorMethodNotAllowed('非疾控用户');
        }
        $apply = $this->model->find($id);
        $check['status'] = $status;

        $check['check_unit'] = $this->company->id;
        $check['check_user'] = $this->user->id;
        $check['check_commnet'] = request()->get('comment');
        $check['check_time'] = Carbon::now()->toDateTimeString();
        $result = $apply->update($check);
        if ($result) {
            if ($status == EquipmentChangeApply::状态_未处理) {
                event(new AutoHandleApply($apply));
            }
            return $this->response->item($apply, new EquipmentChangeApplyTransformer);
        } else {
            return $this->response->errorInternal('系统错误，审核失败');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->check();
        $equipment_change_apply = $this->model->findOrFail($id);
        return $this->response->item($equipment_change_apply, new EquipmentChangeApplyTransformer());
    }

    public function statistics()
    {
        $this->check();
        $result = $this->model->getStatistics($this->company_ids);
        return $this->response->array($result);
    }

    public function getApplyStatus()
    {
        $result=[];
        foreach(EquipmentChangeApply::STATUS as $key=> $status)
        {
            $result[]=[
                'value'=>$key,
                'label'=>$status,
            ];
        }
        return $this->response->array($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
