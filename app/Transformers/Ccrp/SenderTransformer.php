<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Sender;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class SenderTransformer extends TransformerAbstract
{
    protected $availableIncludes=['cooler_category','dccharging','warning_setting'];
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
    public function includeCoolerCategory(Sender $sender)
    {
        return $this->item($sender->cooler_category,new CoolerCategoryTransformer());
    }
    public function includeDccharging(Sender $sender)
    {
        return $this->item($sender->dccharging,new DcchargingTransformer());
    }
    public function includeWarningSetting(Sender $sender)
    {
        if ($sender->warning_setting)
        return $this->item($sender->warning_setting,new SenderWarningSettingTransformer());
        else
        return new Item(null,function (){
            return [];
        });
    }
}