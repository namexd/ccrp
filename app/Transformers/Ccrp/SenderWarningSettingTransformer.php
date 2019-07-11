<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\SenderWarningSetting;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class SenderWarningSettingTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['warninger'];
    public function transform(SenderWarningSetting $setting)
    {
        return [
            'sender_id'=>$setting->sender_id,
            'power_warning'=>$setting->power_warning,
            'power_warning_last'=>$setting->power_warning_last,
            'power_warning2_last'=>$setting->power_warning2_last,
            'power_warning3_last'=>$setting->power_warning3_last,
            'set_time'=>$setting->set_time,
            'set_uid'=>$setting->set_uid,
            'warninger_id'=>$setting->warninger_id,
            'warninger2_id'=>$setting->warninger2_id,
            'warninger3_id'=>$setting->warninger3_id,
            'category_id'=>$setting->category_id,
            'company_id'=>$setting->company_id,
            'status'=>$setting->status,

        ];
    }

    public function includeWarninger(SenderWarningSetting $setting)
    {
        if ($setting->warninger)
        return $this->item($setting->warninger, new WarningerTransformer());
        else
            return new Item(null,function (){
                return [];
            });
    }

}
