<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Sender;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SenderTransformer extends TransformerAbstract
{
    public function transform(Sender $sender)
    {
        return [
            'id' => $sender->id,
            'sn' => $sender->sender_id,
            'name' => $sender->note,
            'simcard' => $sender->simcard,
            'ischarging' => $sender->ischarging,
            'ischarging_update_time' =>$sender->ischarging_update_time>0?Carbon::createFromTimestamp($sender->ischarging_update_time)->toDateTimeString():0,
            'company' => $sender->company->title,
            'status' => $sender->status,
            'created_at' => $sender->install_time>0?Carbon::createFromTimestamp($sender->install_time)->toDateTimeString():0,
            'updated_at' => $sender->update_time>0?Carbon::createFromTimestamp($sender->update_time)->toDateTimeString():0,
        ];
    }
}