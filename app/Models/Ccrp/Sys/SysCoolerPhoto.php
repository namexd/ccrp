<?php


namespace App\Models\Ccrp\Sys;


use App\Models\Ccrp\Coldchain2ModelWithTimestamp;

class SysCoolerPhoto extends Coldchain2ModelWithTimestamp
{
   protected $table='sys_cooler_photos';

    protected $fillable =[
        'id',
        'name',
        'category',
        'value',
        'slug',
        'description',
        'note'
    ];
}