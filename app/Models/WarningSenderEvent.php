<?php

namespace App\Models;
use App\Traits\ControllerDataRange;
use function App\Utils\request;

class WarningSenderEvent extends Coldchain2Model
{
    const TIME_FIELD ='sensor_event_time';

    const 已处理=1;
    const 未处理=0;

    use ControllerDataRange;
    public function __construct()
    {
        parent::__construct();
        $this->set_default_datas(request()->date_name??'最近30天');
    }
    protected $table = 'warning_sender_event';
    protected $primaryKey = 'logid';

    function sender()
    {
        return $this->belongsTo(Sender::class, 'sender_id', 'sender_id')->where('company_id', $this->company_id);
    }

    function options()
    {
        return $this->hasMany(WarningEventOption::class, 'warning_type', 'warning_type')->where('warning_type',WarningEvent::断电预警);
    }
}
