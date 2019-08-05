<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;

class SysCoolerBrand extends Coldchain2ModelWithTimestamp
{
    protected $table = 'sys_cooler_brands';
    protected $fillable = [
        'name',
        'slug',
        'comporation',
        'has_medical',
        'popularity'
    ];

    public function models()
    {
        return $this->hasMany(SysCoolerModel::class,'brand_id','id')->orderBy('is_medical','desc')->orderBy('popularity','desc');

    }
}
