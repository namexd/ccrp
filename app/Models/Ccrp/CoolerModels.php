<?php

namespace App\Models\Ccrp;
use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
use App\Models\Ccrp\Sys\SysCoolerModel;

class CoolerModels extends Coldchain2ModelWithTimestamp
{
    protected $table = "cooler_models";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =['id','sys_brand_id','user_model','popularity'];
}
