<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\WarningSetting;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningSettingTransformer extends TransformerAbstract
{
    public function transform(WarningSetting $setting)
    {
        return [
            'id' => $setting->id,
            'temp_low' => $setting->temp_low,
            'temp_high' => $setting->temp_high,
            'temp_warning' => $setting->temp_warning,
            'status' => $setting->status,
            'created_at' =>$setting->set_time?Carbon::createFromTimestamp($setting->set_time)->toDateTimeString():'',
        ];
    }
}