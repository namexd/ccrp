<?php

namespace App\Models\Ccrp;

class WarningSetting extends Coldchain2Model
{
    protected $table = 'warning_setting';
    public $timestamps = false;
    protected $fillable = [
        'collector_id',
        'warning_strategy_id',
        'temp_warning',
        'humi_warning',
        'volt_warning',
        'temp_high',
        'temp_low',
        'humi_high',
        'humi_low',
        'volt_high',
        'volt_low',
        'temp_warning_last',
        'temp_warning2_last',
        'temp_warning3_last',
        'humi_warning_last',
        'humi_warning2_last',
        'humi_warning3_last',
        'volt_warning_last',
        'set_time',
        'set_uid',
        'warninger_id',
        'warninger2_id',
        'warninger3_id',
        'category_id',
        'company_id',
        'status',
        'note',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class, 'collector_id', 'collector_id');
    }

    public function warninger()
    {
        return $this->belongsTo(Warninger::class, 'warninger_id', 'warninger_id');
    }

   const WARNING_TIME = [
        'WARNING_TIME_LAST' => array(
            '1' => 30,
            '2' => 60,
            '3' => 60,
        ),
        'POWER_WARNING_TIME_LAST' => array(
            '1' => 0,
            '2' => 60,
            '3' => 60,
        ),
        'DELIVERORDER_WARNING_TIME_LAST' => array(
            '1' => 5,
            '2' => 10,
            '3' => 15,
        ),
    ];
}
