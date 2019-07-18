<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\EquipmentChangeApply;
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
            'user_sign' => $apply->userSign ?? '',
            'details' => $apply->details,
            'news' => $apply->news,
            'status' => $apply->status,
            'status_name' => $apply::STATUS[$apply->status],
        ];
        if ($apply->handler) {
            $rs['handler'] = $apply->user->name;
            $rs['end_time'] = $apply->end_time;
        }
        return $rs;
    }

    public function includeDetails(EquipmentChangeApply $apply)
    {
        return $this->collection($apply->details, new EquipmentChangeDetailTransformer());
    }
}