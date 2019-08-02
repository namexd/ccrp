<?php

namespace App\Models\Ccrp\Sys;

//use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
use Illuminate\Database\Eloquent\Model;
class SysCoolerBrand extends Model
{
  protected $table='sys_cooler_brands';
  protected $fillable=[
      'name',
      'slug',
      'comporation',
      'has_medical',
    'popularity'
  ];
}
