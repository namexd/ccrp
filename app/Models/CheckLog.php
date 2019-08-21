<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckLog extends Model
{
    protected $fillable=[ 'check_id', 'check_result','status'];

    public function check()
    {
        return $this->belongsTo(Check::class);
    }
}
