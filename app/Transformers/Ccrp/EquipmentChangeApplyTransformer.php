<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\EquipmentChangeApply;
use App\Models\User;
use App\Models\UserHasApp;
use League\Fractal\TransformerAbstract;

class EquipmentChangeApplyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['details'];

    public function transform(EquipmentChangeApply $apply)
    {
        $rs = [
            'id' => $apply->id,
            'company' => $apply->company->title,
            'phone' => $apply->phone,
            'apply_time' => $apply->apply_time,
            'user_id' => $apply->user_id,
            'user_name' => $apply->user_name,
            'user_sign' => $apply->user_sign ? config('app.we_url').'/files/'.$apply->user_sign : '',
            'details' => $apply->details,
            'news' => $apply->news,
            'status' => $apply->status,
            'status_name' => $apply::STATUS[$apply->status],
            'check_unit' => $apply->checkUnit ? $apply->checkUnit->title : '',
            'check_user' => $apply->checkUser ? $apply->checkUser->name : '',
            'check_commnet' => $apply->check_commnet,
            'check_time' => $apply->check_time,
            'handler' => $this->getHandler($apply->handler),
            'end_time' => $apply->end_time,
            'comment' => $apply->comment,
            'is_auto' => $apply->is_auto
        ];
        return $rs;
    }

    public function includeDetails(EquipmentChangeApply $apply)
    {
        return $this->collection($apply->details, new EquipmentChangeDetailTransformer());
    }
    public function getHandler($handler)
    {
       $userhasapp= UserHasApp::where('app_id',1)->where('app_userid',$handler)->first();
       if ($userhasapp)
       {
           return User::find($userhasapp->user_id)->realname;
       }else
           return '';
    }
}