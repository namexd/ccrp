<?php

namespace App\Models\Ccrp;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
use App\Models\Ccrp\Sys\SysCoolerModel;

class Deliver extends Coldchain2ModelWithTimestamp
{
    protected $table = "deliver";
    protected $primaryKey = 'deliver_id';
    public $timestamps = false;

    protected $fillable = [
        'deliver',
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
