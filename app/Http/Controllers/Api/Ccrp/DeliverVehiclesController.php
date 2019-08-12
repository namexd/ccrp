<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\DeliverVehicleRequest;
use App\Models\Ccrp\DeliverVehicle;
use App\Models\Ccrp\DeliverVehicleLog;
use App\Transformers\Ccrp\DeliverVehicleTransformer;

class DeliverVehiclesController extends Controller
{
    private $model;

    public function __construct(DeliverVehicle $delivervehicle)
    {
        $this->model = $delivervehicle;
    }

    public function index()
    {
        $this->check();
        $delivervehicle = $this->model->whereIn('company_id', $this->company_ids)->where('status', 1);
        if ($keyword = request()->get('keyword')) {
            $delivervehicle = $delivervehicle->where('driver', 'like', '%'.$keyword.'%')->whereOr('vehicle', 'like', '%'.$keyword.'%');
        }
        $delivervehicle = $delivervehicle->orderBy('delivervehicle_id', 'desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($delivervehicle, new DeliverVehicleTransformer());
    }

    public function show($id)
    {
        $this->check();
        $delivervehicle = $this->model->find($id);
        return $this->response->item($delivervehicle, new DeliverVehicleTransformer());
    }

    public function update($id)
    {
        $this->check();
        $delivervehicle = $this->model->find($id);
        $this->authorize('unit_operate', $delivervehicle->company);
        $request = request()->all();
        $result = $delivervehicle->update($request);
        if ($result) {
            return $this->response->item($delivervehicle, new DeliverVehicleTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(DeliverVehicleRequest $request)
    {
        $this->check();
        $request['create_uid'] = $this->user->id;
        $request['status'] = 1;
        $request['create_time'] = time();
        $request['company_id'] = $this->company->id;
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new DeliverVehicleTransformer())->setStatusCode(201);
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

}
