<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\WarningSendlog;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\WarningSendlogTransformer;

class WarningSendlogsController extends Controller
{
    use ControllerDataRange;
    public $default_date = '最近30天';

    public function index($type = 'index')
    {
        $this->check();
        $model = WarningSendlog::whereIn('company_id', $this->company_ids);
        switch ($type) {
            case  'index':
                break;
            case  'overtemp':
                $model->where('event_type', '温度报警');
                break;
            case  'poweroff':
                $model->whereIn('event_type', ['市电断电' , '市电上电']);
                break;
            case  'offline':
                $model->where('event_type', '离线报警');
                break;
            default :
                break;
        }
        $this->set_default_datas(request()->date_name?? $this->default_date);
        $model= $model->whereBetween(WarningSendlog::TIME_FIELD,$this->get_dates());
        $logs = $model->orderBy('id', 'desc')->paginate($this->pagesize);
        return $this->response->paginator($logs, new WarningSendlogTransformer())->addMeta('date_range',$this->get_dates('datetime',true));
    }

    public function show(WarningSendlog $sendlog)
    {
        $this->check();
        return in_array($sendlog->company_id,$this->company_ids)?$this->response->item($sendlog, new WarningSendlogTransformer()):$this->response->noContent();
    }


}
