<?php

namespace App\Models\Ccrp;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
use App\Models\Ccrp\Sys\SysCoolerModel;

class DeliverVehicle extends Coldchain2ModelWithTimestamp
{
    protected $table = "DeliverVehicle";
    protected $primaryKey = 'delivervehicle_id';
    public $timestamps = false;

    protected $fillable = [
        'vehicle',
        'driver',
        'suborder',
        'phone',
        'note',
        'company_id',
        'create_uid',
        'create_time',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
