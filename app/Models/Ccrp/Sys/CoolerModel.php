<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;


class CoolerModel extends Coldchain2ModelWithTimestamp
{

    protected $fillable =[
        'id',
        'name',
        'category',
        'slug',
        'description',
        'note'
    ];
    public function brand()
    {
        return $this->belongsTo(CoolerBrand::class);
    }
    public function type()
    {
        return $this->belongsTo(CoolerType::class);
    }
}
