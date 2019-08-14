<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;

class EquipmentChangeContact extends Model
{
    protected $fillable=[
        'apply_id',
        'action',
        'contact_id',
        'name',
        'phone',
        'warninger_id',
        'level',
        'bak'
    ];

    public function warninger()
    {
        return $this->belongsTo(Warninger::class,'warninger_id','warninger_id');
    }
    public function getBakAttribute($value)
    {
        return json_decode($value,true);
    }

    public function setBakAttribute($value)
    {
        $this->attributes['bak']=json_encode($value,JSON_UNESCAPED_UNICODE);
    }
}
