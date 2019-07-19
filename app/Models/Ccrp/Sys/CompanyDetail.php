<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2ModelWithTimestamp;

class CompanyDetail extends Coldchain2ModelWithTimestamp
{
  protected $table=['sys_company_details'];
  protected $fillable=[
      'name',
      'slug',
      'value',
      'description',
      'note',
      'sort'
  ];
}
