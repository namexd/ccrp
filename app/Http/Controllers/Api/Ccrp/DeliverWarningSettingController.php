<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\DeliverWarningSettingRequest;
use App\Models\Ccrp\DeliverWarningSetting;
use App\Models\Ccrp\DeliverWarningSettingLog;
use App\Transformers\Ccrp\DeliverWarningSettingTransformer;

class DeliverWarningSettingController extends Controller
{
    private $model;

    public function __construct(DeliverWarningSetting $warningSetting)
    {
        $this->model = $warningSetting;
    }

    public function index()
    {
        $this->check();
        $warningSetting = $this->model->whereIn('company_id', $this->company_ids)->where('status', 1);
        $warningSetting = $warningSetting->orderBy('id', 'desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($warningSetting, new DeliverWarningSettingTransformer());
    }

    public function show($id)
    {
        $this->check();
        $warningSetting = $this->model->find($id);
        return $this->response->item($warningSetting, new DeliverWarningSettingTransformer());
    }

    public function update($id)
    {
        $this->check();
        $warningSetting = $this->model->find($id);
        $this->authorize('unit_operate', $warningSetting->company);
        $request = request()->all();
        $result = $warningSetting->update($request);
        if ($result) {
            return $this->response->item($warningSetting, new DeliverWarningSettingTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(DeliverWarningSettingRequest $request)
    {
        $this->check();
        $request['company_id'] = $this->company->id;
        $request['humi_warning_last'] =$request->get('humi_warning_last',$this->model::DELIVERORDER_WARNING_TIME_LAST['1']);
        $request['humi_warning2_last'] =$request->get('humi_warning2_last',$this->model::DELIVERORDER_WARNING_TIME_LAST['2']);
        $request['humi_warning3_last'] =$request->get('humi_warning3_last',$this->model::DELIVERORDER_WARNING_TIME_LAST['3']);
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new DeliverWarningSettingTransformer())->setStatusCode(201);
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

}
