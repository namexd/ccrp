<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;


class SysCoolerDetail extends Coldchain2ModelWithTimestamp
{
    protected $table = 'sys_cooler_details';
    protected $primaryKey = 'id';

    protected $fillable =[
        'id',
        'name',
        'category',
        'slug',
        'value',
        'description',
        'sort',
        'note'
    ];

    public function getValueAttribute($key)
    {
        return json_decode($key,true);
    }
}
