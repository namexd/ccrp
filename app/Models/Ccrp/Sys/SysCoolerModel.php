<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;


class SysCoolerModel extends Coldchain2ModelWithTimestamp
{
protected $table='sys_cooler_models';
    protected $fillable =[
        'id',
        'name',
        'type_id',
        'brand_id',
        'description',
        'cool_volume',
        'cold_volume',
        'whole_volume',
        'is_medical',
    ];
    public function brand()
    {
        return $this->belongsTo(SysCoolerBrand::class,'brand_id');
    }
    public function type()
    {
        return $this->belongsTo(SysCoolerType::class,'type_id');
    }
}
