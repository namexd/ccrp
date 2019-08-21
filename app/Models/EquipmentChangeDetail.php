<?php

namespace App\Models;

use App\Models\Ccrp\Cooler;
use Illuminate\Database\Eloquent\Model;

class EquipmentChangeDetail extends Model
{
    protected $fillable=[
        'cooler_id',
        'change_type',
        'reason'
    ];
    public function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }
}
