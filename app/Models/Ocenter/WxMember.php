<?php

namespace App\Models\Ocenter;


/**
 * Class Collector
 * @package App\Models
 */
class WxMember extends OcenterModel
{
    protected $table = 'wx_member';
    protected $fillable = ['username', 'truename', 'wxcode', 'unionid', 'uid', 'bind_time', 'nickname', 'sex', 'language', 'city', 'province', '	country', 'headimgurl', 'status','phone','phone_bind_time'];

    public function getOpenidAttribute($value)
    {
        return $this->wxcode;
    }
}
