<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Warninger;
use App\Models\Ccrp\WarningSetting;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class WarningSettingTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['warninger','collector'];
    public function transform(WarningSetting $setting)
    {
        return [
            'id' => $setting->id,
            'temp_low' => $setting->temp_low,
            'temp_high' => $setting->temp_high,
            'temp_warning' => $setting->temp_warning,
            'humi_low' => $setting->humi_low,
            'humi_high' => $setting->humi_high,
            'humi_warning' => $setting->humi_warning,
            'temp_warning_last' => $setting->temp_warning_last,
            'temp_warning2_last' => $setting->temp_warning2_last,
            'temp_warning3_last' => $setting->temp_warning3_last,
            'status' => $setting->status,
            'created_at' =>$setting->set_time?Carbon::createFromTimestamp($setting->set_time)->toDateTimeString():'',
            'warninger_id' => $setting->warninger_id,
            'note' => $setting->note,
        ];
    }

    public function includeWarninger(WarningSetting $setting)
    {
        if ($setting->warninger)
        {
            return $this->item($setting->warninger, new WarningerTransformer());
        }else
        {
            return  $this->null();
        }
    }
    public function includeCollector(WarningSetting $setting)
    {
        return $this->item($setting->collector,new CollectorTransformer());
    }
}
