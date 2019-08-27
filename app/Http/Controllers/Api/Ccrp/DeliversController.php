<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\DeliverRequest;
use App\Models\Ccrp\Deliver;
use App\Models\Ccrp\GatewaybindingdataModel;
use App\Models\Ccrp\DeliverLog;
use App\Transformers\Ccrp\DeliverTransformer;
use Illuminate\Support\Facades\Input;

class DeliversController extends Controller
{
    private $model;

    public function __construct(Deliver $deliver)
    {
        $this->model = $deliver;
    }

    public function index()
    {
        $this->check();
        $deliver = $this->model->whereIn('company_id', $this->company_ids);
        if ($keyword = request()->get('keyword')) {
            $deliver = $deliver->where('deliver', 'like', '%'.$keyword.'%')->whereOr('phone', 'like', '%'.$keyword.'%');
        }
        $deliver = $deliver->orderBy('deliver_id', 'desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($deliver, new DeliverTransformer());
    }

    public function show($id)
    {
        $this->check();
        $deliver = $this->model->find($id);
        return $this->response->item($deliver, new DeliverTransformer());
    }

    public function update($id)
    {
        $this->check();
        $deliver = $this->model->find($id);
        $this->authorize('unit_operate', $deliver->company);
        $request = request()->all();
        $result = $deliver->update($request);
        if ($result) {
            return $this->response->item($deliver, new DeliverTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(DeliverRequest $request)
    {
        $this->check();
        $request['create_uid'] = $this->user->id;
        $request['status'] = 1;
        $request['create_time'] = time();
        $request['company_id'] = $this->company->id;
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new DeliverTransformer())->setStatusCode(201);
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

}
