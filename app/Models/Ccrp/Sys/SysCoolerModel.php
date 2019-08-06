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
        'popularity',
        'body_type',
        'product_date',
        'medical_licence',
        'picture',
        'comment',
        'warmarea_count'
    ];
    const IS_MEDICAL = [
        0=>'未知',
        1=>'医用',
        2=>'非医用',
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
