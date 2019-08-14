<?php

namespace App\Models\Ccrp;

use App\Models\Ccrp\Cooler;
use Illuminate\Database\Eloquent\Model;

class EquipmentChangeDetail extends Model
{
    protected $fillable=[
        'apply_id',
        'cooler_id',
        'change_type',
        'collector_id',
        'reason'
    ];
    public function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }
    public function apply()
    {
        return $this->belongsTo(EquipmentChangeApply::class,'apply_id','id');
    }

    public function getCollectorIdAttribute($value)
    {
        return json_decode($value,true);
    }
    public function setCollectorIdAttribute($value)
    {
        $this->attributes['collector_id']=json_encode($value);
    }
}
