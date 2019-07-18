<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Events\AutoHandleApply;
use App\Models\App;
use App\Models\Ccrp\EquipmentChangeApply;
use App\Transformers\Ccrp\EquipmentChangeApplyTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EquipmentChangeApplyController extends Controller
{
    protected $model;

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
        if ($status = $request->get('status')) {
            if (!is_array($status)) {
                $status = [$status];
            }
            $this->model = $this->model->whereIn('status', $status);
        }
        if ($request->start_time && $request->end_time) {
            $this->model = $this->model->whereBetween('apply_time', [$request->start_time, $request->end_time]);
        }
        $data = $this->model->with(['company', 'details', 'news'])->whereIn('company_id', $company_ids)->orderBy('id', 'desc')->paginate($request->pagesize ?? $this->pagesize);
        return $this->response->paginator($data, new EquipmentChangeApplyTransformer());
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
            return $this->response->errorUnauthorized('非疾控用户');
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
        $app = App::where('program', 'microservice_file')->first();
        $url = $equipment_change_apply->user_sign ? $app->api_url.'upload/'.$equipment_change_apply->user_sign : '';
        return $this->response->item($equipment_change_apply, new EquipmentChangeApplyTransformer())->addMeta('sign_url', $url);
    }

    public function statistics()
    {
        $this->check();
        $result = $this->model->getStatistics($this->company_ids);
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
