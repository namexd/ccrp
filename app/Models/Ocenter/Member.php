<?php

namespace App\Models\Ocenter;


/**
 * Class Collector
 * @package App\Models
 */
class Member extends OcenterModel
{
    protected $table = 'member';
    protected $fillable = ['nickname', 'sex', 'status', 'reg_time', 'app_id'];

    public function getOpenidAttribute($value)
    {
        return $this->wxcode;
    }
}
