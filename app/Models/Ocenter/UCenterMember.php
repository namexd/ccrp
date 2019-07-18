<?php

namespace App\Models\Ocenter;


/**
 * Class Collector
 * @package App\Models
 */
class UCenterMember extends OcenterModel
{
    protected $table = 'ucenter_member';
    protected $fillable = ['username', 'password', 'mobile', 'reg_time', 'reg_ip', 'status', 'type', 'sex', 'language', 'city', 'province', '	country', 'headimgurl', 'status','phone','phone_bind_time'];

    public function getOpenidAttribute($value)
    {
        return $this->wxcode;
    }
}
