<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ccrp\Sys\SysCoolerModel;

class CoolerModels extends Model
{
    protected $table = "cooler_models";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =['id','sys_brand_id','user_model','popularity'];
}
