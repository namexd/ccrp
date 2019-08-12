<?php

namespace App\Models\Ccrp;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
use App\Models\Ccrp\Sys\SysCoolerModel;

class DeliverOrder extends Coldchain2ModelWithTimestamp
{
    protected $table = "deliverorder";
    protected $primaryKey = 'deliverorder_id';
    public $timestamps = false;

    protected $fillable = [
        'deliverorder',
        'deliverorder_main',
        'suborder',
        'cooler_id',
        'collector_id',
        'customer_id',
        'customer_name',
        'delivervehicle_id',
        'delivervehicle',
        'deliver_id',
        'deliver',
        'deliver_goods',
        'temp_low',
        'temp_high',
        'company_id',
        'create_uid',
        'create_time',
        'finished',
        'finished_time',
        'finished_note',
        'deliverorder_warning_setting',
        'temp_fix',
        'status',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class,'collector_id','collector_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    public function warningSetting()
    {
        return $this->belongsTo(DeliverWarningSetting::class,'deliverorder_warning_setting','id');
    }
}
