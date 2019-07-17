<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;

class CoolerBrand extends Coldchain2ModelWithTimestamp
{
  protected $table=['sys_cooler_brands'];
  protected $fillable=[
      'name',
      'slug',
      'comporation',
      'has_medical',
  ];
}
