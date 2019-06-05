<?php

namespace App\Models\Ccrp;
use App\Traits\ControllerDataRange;

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
        return $this->belongsTo(Sender::class, 'sender_pk_id', 'id')->where('company_id', $this->company_id);
    }

    function options()
    {
        return $this->hasMany(WarningEventOption::class, 'warning_type', 'warning_type')->where('warning_type',WarningEvent::断电预警);
    }

    public static function lists($company_ids,$handled=null)
    {
        $res =  self::whereIn('company_id', $company_ids)->where('warning_type',0);
        if($handled !== null)
        {
            $res = $res->where('handled', $handled);
        }
        return $res;
    }
}
