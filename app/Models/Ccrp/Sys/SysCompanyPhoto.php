<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;

class SysCompanyPhoto extends Coldchain2ModelWithTimestamp
{
  protected $table='sys_company_photos';

  protected $fillable=[
      'name',
      'slug',
      'value',
      'description',
      'note',
      'sort'
  ];
}
