<?php

namespace App\Models\Ccrp;


class Category extends Coldchain2Model
{
    protected $table = 'cooler_category';
    function coolers(){
    return $this->hasMany(Cooler::class,'category_id','id')
        ->where([
            'status'=>['neq',4],
            'cooler_type'=>['lt',100],
            'collector_num'=>['gt',0]
        ])
        ->field('category_id,cooler_id,cooler_name,cooler_sn,cooler_img,cooler_brand,cooler_size,cooler_size2,cooler_model,is_medical,cooler_starttime,status,company_id');
    }
    function company()
    {
        return $this->belongsTo(Company::class,'id','company_id');
    }
}
