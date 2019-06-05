<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Cooler;
use App\Traits\ModelFields;

class StatCooler extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'stat_cooler';

    public function cooler()
    {

        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }

    static public function fieldTitles()
    {
        return [
            'month' => '年月',
            'temp_avg'=>'平均温度',
            'temp_high'=>'最高温度',
            'temp_low'=>'最低温度',
            'error_times'=>'设备故障次数',
            'warning_times'=>'超温预警次数',
            'temp_variance'=>'冷链设备评估值',
        ];
    }
}
