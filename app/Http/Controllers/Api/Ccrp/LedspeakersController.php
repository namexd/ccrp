<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\LedspeakerRequest;
use App\Models\Ccrp\GatewaybindingdataModel;
use App\Models\Ccrp\Ledspeaker;
use App\Models\Ccrp\LedspeakerLog;
use App\Transformers\Ccrp\LedspeakerTransformer;
use Illuminate\Support\Facades\Input;

class LedspeakersController extends Controller
{
    private $model;

    public function __construct(Ledspeaker $ledspeaker)
    {
        $this->model = $ledspeaker;
    }

    public function index()
    {
        $this->check();
        $ledspeaker = $this->model->whereIn('company_id', $this->company_ids)->where('status', 1);
        if ($keyword = request()->get('keyword')) {
            $ledspeaker = $ledspeaker->where('ledspeaker_name', 'like', '%'.$keyword.'%')->orWhere('supplier_ledspeaker_id', 'like', '%'.$keyword.'%');
        }
        $ledspeaker = $ledspeaker->orderBy('ledspeaker_id', 'desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($ledspeaker, new LedspeakerTransformer())->addMeta('ledspeaker_module', $this->model->getLedspeaker_module());
    }

    public function show($id)
    {
        $this->check();
        $warning = $this->model->find($id);
        return $this->response->item($warning, new LedspeakerTransformer())->addMeta('ledspeaker_module', $this->model->getLedspeaker_module());
    }

    public function update($id)
    {
        $this->check();
        $ledspeaker = $this->model->find($id);
        $this->authorize('unit_operate', $ledspeaker->company);
        $request = request()->all();
        if (array_has($request, 'collector_id')) {
            $request['collector_num'] = count(explode(',', request()->get('collector_num')));

        } else {
            $request['collector_num'] = 0;
            $request['collector_id'] = '';
        }
        $request['update_time'] = time();
        $result = $ledspeaker->update($request);
        if ($result) {
            return $this->response->item($ledspeaker, new LedspeakerTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(LedspeakerRequest $request)
    {
        $this->check();
        $request['supplier_ledspeaker_id'] = str_replace('-', '', $request['supplier_ledspeaker_id']);
        $request['install_uid'] = $this->user->id;
        $request['install_time'] = time();
        $request['company_id'] = $this->company->id;
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new LedspeakerTransformer());
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

    public function destroy($id)
    {
        if (Input::get('change_note') == '')
            return $this->response->errorBadRequest('备注不能为空');
        $ledspeaker = $this->model->find($id);
        if ($ledspeaker->status == 2) {
            return $this->response->errorBadRequest('该报警器已经报废');
        }
        $attribute['change_time'] = time();
        $attribute['change_option'] = 1;
        $logmodel = LedspeakerLog::create($attribute);
        $supplier_ledspeaker_id = is_numeric($ledspeaker->supplier_ledspeaker_id) ? $ledspeaker->supplier_ledspeaker_id : 0;
        if ($logmodel) {
            $ledspeaker->supplier_ledspeaker_id = -1 * $supplier_ledspeaker_id;
            $ledspeaker->status = 2;
            $ledspeaker->uninstall_time = time();
            $ledspeaker->save();
        }
        return $this->response->noContent();
    }

    public function bind($id)
    {
        $ledspeaker = $this->model->find($id);
        $model = new GatewaybindingdataModel();
        $map['GatewayMac'] = $model->format_mac($ledspeaker['supplier_ledspeaker_id']);
        $document_list = $model
            ->where($map)
            ->orderBy('status', 'asc')->orderBy('id', 'desc')
            ->get();
        return $this->response->array($document_list);
    }

    public function products()
    {
        return $this->response->array(['data' => $this->model->get_products()]);
    }
}
