<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;


class SysCoolerType extends Coldchain2ModelWithTimestamp
{
    protected $table = 'sys_cooler_types';
    protected $primaryKey = 'id';

    protected $fillable =[
        'id',
        'name',
        'category',
        'slug',
        'description',
        'note'
    ];

}
