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
    const CATEGORIES = [
        '基础档案' => '基础档案',
        '免规档案' => '免规档案',
        '设备信息' => '设备信息',
        '医用冰箱' => '医用冰箱',
        '冷库信息' => '冷库信息',
        '供电信息' => '供电信息',
        '设备照片' => '设备照片',
        '填报人' => '填报人'
    ];
    public function getValueAttribute($key)
    {
        return json_decode($key,true);
    }
    public static function columns($value = 'name', $key = 'slug')
    {
        return self::all()->pluck($value, $key)->toArray();
    }
}
