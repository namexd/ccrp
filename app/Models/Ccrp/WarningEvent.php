<?php

namespace App\Models\Ccrp;

use Carbon\Carbon;

class WarningEvent extends Coldchain2Model
{
    protected $table = 'warning_event';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'collector_id', 'temp_warning', 'humi_warning', 'volt_warning', 'temp_event', 'humi_event', 'volt_event', 'temp_last', 'humi_last', 'volt_last', 'sensor_collect_time', 'warning_event_time', 'warning_last_time', 'warning_level', 'warning_type', 'temp_high', 'temp_low', 'humi_high', 'humi_low', 'volt_high', 'volt_low', 'handled', 'handled_time', 'handler', 'handler_note', 'handler_same_do', 'handler_uid', 'handler_type', 'category_id', 'company_id', 'system_type'];


    const TIME_FIELD = 'warning_event_time';

    const 高温预警 = 1;
    const 低温预警 = 2;
    const 高湿度预警 = 3;
    const 低湿度预警 = 4;
    const 断电预警 = 5;
    const 低压预警 = 6;
    const 高压预警 = 7;
    const WARNING_TYPE = [
        self::高温预警 => '高温预警',
        self::低温预警 => '低温预警',
        self::高湿度预警 => '高湿度预警',
        self::低湿度预警 => '低湿度预警',
        self::断电预警 => '断电预警',
        self::低压预警 => '低压预警',
        self::高压预警 => '高压预警'
    ];
    const 已处理=1;
    const 未处理=0;

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    function collector()
    {
        return $this->belongsTo(Collector::class, 'collector_id', 'collector_id');
    }

    function options()
    {
        return $this->hasMany(WarningEventOption::class, 'warning_type', 'warning_type');
    }

    public static function lists($company_ids,$handled=null)
    {
        $res =  self::whereIn('company_id', $company_ids);
        if($handled !== null)
        {
            $res = $res->where('handled', $handled);
        }
        return $res;
    }
    public function getYesterdayTempStat($company_ids)
    {
        $start=Carbon::yesterday()->startOfDay()->timestamp;
        $end=Carbon::yesterday()->endOfDay()->timestamp;
        $result=$this->selectRaw('
        ifnull(sum(if(warning_type=1,1,0)),0) as high_temp,
        ifnull(sum(if(warning_type=2,1,0)),0) as low_temp')
            ->whereIn('company_id',$company_ids)
            ->whereBetween('warning_event_time',[$start,$end])
            ->first();
        return $result;
    }

    public function checkWarning($company_id = null)
    {
        $start = Carbon::now()->subDays(3)->timestamp;
        $end = time();
        if ($company_id === null) {
            $query = $this->whereNotIn('company_id',Company::getUnwatchIds())->selectRaw('"company_id" as object_key,company_id as object_value,count(collector_id) as result')->whereBetween('warning_event_time', [$start, $end])->where('warning_type', 1)->groupBy('company_id')->havingRaw('count(collector_id)>?', [10])->get();
            return $query;
        } else {
            $query = $this->whereBetween('warning_event_time', [$start, $end])->where('warning_type', 1)->where('company_id', $company_id)
                ->count();
            $result = new StdClass();
            $result->object_key = 'company_id';
            $result->object_value = $company_id;
            $result->result = $query>9?$query:0;
            return $result;

        }
    }
}
