<?php

namespace App\Transformers;

use App\Models\User;
use function App\Utils\hidePhone;
use League\Fractal\TransformerAbstract;

class UserHidePhoneTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $rs= [
            'id' => $user->id,
            'name' => $user->name,
            'realname' => $user->realname,
            'phone' => hidePhone($user->phone),
            'phone_verified' => $user->phone_verified ,
            'headimgurl'=>$user->weuser->headimgurl,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
        if(request()->get('with'))
        {
            unset($rs['phone_verified']);
        }
        return $rs;
    }
}