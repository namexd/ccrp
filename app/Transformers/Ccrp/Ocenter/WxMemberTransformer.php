<?php

namespace App\Transformers\Ccrp\Ocenter;

use App\Models\Ccrp\Contact;
use App\Models\Ocenter\WxMember;
use function App\Utils\hidePhone;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WxMemberTransformer extends TransformerAbstract
{
    public function transform(WxMember $wxMember)
    {
        $rs = [
            'id'=>$wxMember->id,
            'username'=>$wxMember->username,
            'truename'=>$wxMember->truename,
            'wxcode'=>$wxMember->wxcode,
            'unionid'=>$wxMember->unionid,
            'uid'=>$wxMember->uid,
            'bind_time'=>$wxMember->bind_time,
            'nickname'=>$wxMember->nickname,
            'sex'=>$wxMember->sex,
            'language'=>$wxMember->language,
            'city'=>$wxMember->city,
            'province'=>$wxMember->province,
            'country'=>$wxMember->country,
            'headimgurl'=>$wxMember->headimgurl,
            'status'=>$wxMember->status,
            'phone'=>$wxMember->phone,
            'phone_bind_time'=>$wxMember->phone_bind_time
        ];
        return $rs;
    }
}
