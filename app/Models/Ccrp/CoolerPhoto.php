<?php
namespace App\Models\Ccrp;


use App\Models\Ccrp\Sys\SysCoolerPhoto;

class CoolerPhoto extends Coldchain2ModelWithTimestamp
{
    protected $table = 'cooler_photos';

    protected $fillable = ['category','name','slug','value','description','note','sort'];

    public function sys_photo()
    {
        return $this->belongsTo(SysCoolerPhoto::class,'sys_id','id');
    }
    public function getValueAttribute($value)
    {
        return $value ? config('app.we_url').'/files/'.$value : '';

    }
}
