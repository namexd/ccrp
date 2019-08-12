<?php

namespace App\Models\Ccrp;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
use App\Models\Ccrp\Sys\SysCoolerModel;

class DeliverWarningSetting extends Coldchain2ModelWithTimestamp
{
    protected $table = "deliverorder_warning_setting";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'setting_name',
        'temp_warning',
        'humi_warning',
        'temp_high',
        'temp_low',
        'humi_high',
        'humi_low',
        'temp_warning_last',
        'temp_warning2_last',
        'temp_warning3_last',
        'humi_warning_last',
        'humi_warning2_last',
        'humi_warning3_last',
        'volt_warning_last',
        'warninger_id',
        'warninger2_id',
        'warninger3_id',
        'company_id',
        'status',
    ];
    const DELIVERORDER_WARNING_TIME_LAST = [
        '1' => 5,
        '2' => 10,
        '3' => 15,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function warninger()
    {
        return $this->belongsTo(Warninger::class, 'warninger_id', 'warninger_id');
    }
}
