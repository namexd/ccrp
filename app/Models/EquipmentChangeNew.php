<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentChangeNew extends Model
{
    protected $fillable=[
        'cooler_name',//冰箱名称
        'country_code',//免疫规划编号
        'cooler_brand',//品牌
        'cooler_model',//型号
        'cooler_type',//类型
        'cooler_size',//容积(冷藏)
        'cooler_size2',//容积(冷冻)
        'cooler_starttime',//启用日期
        'is_medical'//是否为医用冰箱
    ];
    protected  $casts=[
        'is_medical'=>'boolean'
    ];
}
