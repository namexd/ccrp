<?php

namespace App\Models\Ccrp;

//use Illuminate\Database\Eloquent\Model;
use App\Models\Ccrp\Sys\SysCoolerBrand;
use App\Models\Ccrp\Coldchain2ModelWithTimestamp;
class CoolerBrands extends Coldchain2ModelWithTimestamp
{
    protected $table = "cooler_brands";
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =['id', 'sys_brand','user_brand', 'popularity','is_approved'];
    public function sys_cooler_brand(){
        return $this->belongsTo(SysCoolerBrand::class,'sys_brand','name');
    }
}

