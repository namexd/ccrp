<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\LedspeakerRequest;
use App\Http\Requests\Api\Ccrp\SenderRequest;
use App\Models\Ccrp\Dccharging;
use App\Models\Ccrp\GatewaybindingdataModel;
use App\Models\Ccrp\LedspeakerLog;
use App\Models\Ccrp\Sender;
use App\Transformers\Ccrp\SenderNewTransformer;
use App\Transformers\Ccrp\SenderWarningSettingTransformer;
use Illuminate\Support\Facades\Input;

class SendersController extends Controller
{
    private $model;

    public function __construct(Sender $sender)
    {
        $this->model = $sender;
    }

    public function index()
    {
        $this->check();
        $sender = $this->model->whereIn('company_id', $this->company_ids)->where('status', 1);
        $sender = $sender->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($sender, new SenderNewTransformer());
    }

    public function show($id)
    {
        $this->check();
        $warning = $this->model->find($id);
        return $this->response->item($warning, new SenderNewTransformer());
    }

    public function update($id)
    {
        $this->check();
        $request = request()->all();
        $request['update_time']=time();
        $sender = $this->model->find($id);
        $result = $sender->update($request);
        if ($result) {
            return $this->response->item($sender, new SenderNewTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(SenderRequest $request)
    {
        $this->check();
        $request['install_time']=time();
        $request['install_uid'] = $this->user->id;
        $request['company_id'] = $this->company->id;
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new SenderNewTransformer())->setStatusCode(201);
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

    public function destroy($id)
    {
        $sender=$this->model->find($id);
        if ( $sender->status==2)
        {
            return $this->response->errorBadRequest('该中继器已被删除');
        }
        $sender->status=2;
        $sender->uninstall_time=time();
        $sender->save();
        $sender->warning_setting()->update(['status'=>2]);
        return $this->response->noContent();
    }

    public function warningSetting($id)
    {
        $this->check();
        $request=request()->all();
        $sender = $this->model->find($id);
        if ($sender)
        {
            $request['company_id']=$this->company->id;
            $request['set_time']=time();
            $request['set_uid']=$this->user->id;
            if ($sender->warning_setting)
                $sender->warning_setting()->update($request);
            else
                $sender->warning_setting()->create($request);
        }else
        {
            return $this->response->errorBadRequest('中继器不存在');
        }


        return $this->response->item($sender->warning_setting,new SenderWarningSettingTransformer());
    }

    public function products()
    {
        return $this->response->array(['data' => $this->model->get_products()]);
    }
}
