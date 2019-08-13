<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weuser extends Model
{
    public function weapp()
    {
        return $this->hasManyThrough(Weapp::class,WeappHasWeuser::class);
    }

    public function weid()
    {
        return $this->hasOne(WeappHasWeuser::class);
    }
    public function weids()
    {
        return $this->hasMany(WeappHasWeuser::class);
    }

}
