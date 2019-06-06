<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;

class EquipmentChangeNew extends Model
{
    protected $fillable=[
        'apply_id',
        'cooler_name',
        'country_code',
        'cooler_brand',
        'cooler_model',
        'cooler_type',
        'cooler_size',
        'cooler_size2',
        'cooler_starttime',
        'is_medical'
    ];
    protected  $casts=[
        'is_medical'=>'boolean'
    ];}
