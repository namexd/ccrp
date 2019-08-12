<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\DeliverWarningSetting;
use League\Fractal\TransformerAbstract;

class DeliverWarningSettingTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['warninger','company'];

    public function transform(DeliverWarningSetting $setting)
    {
        $rs =  [
            'id' => $setting->id,
            'setting_name'=>$setting->setting_name,
            'temp_warning'=>$setting->temp_warning,
            'humi_warning'=>$setting->humi_warning,
            'temp_high'=>$setting->temp_high,
            'temp_low'=>$setting->temp_low,
            'humi_high'=>$setting->humi_high,
            'humi_low'=>$setting->humi_low,
            'temp_warning_last'=>$setting->temp_warning_last,
            'temp_warning2_last'=>$setting->temp_warning2_last,
            'temp_warning3_last'=>$setting->temp_warning3_last,
            'humi_warning_last'=>$setting->humi_warning_last,
            'humi_warning2_last'=>$setting->humi_warning2_last,
            'humi_warning3_last'=>$setting->humi_warning3_last,
            'volt_warning_last'=>$setting->volt_warning_last,
            'warninger_id'=>$setting->warninger_id,
//            'warninger2_id'=>$setting->warninger2_id,
//            'warninger3_id'=>$setting->warninger3_id,
            'company_id'=>$setting->company_id,
            'status'=>$setting->status,
        ];

        return $rs;
    }
    
    public function includeCompany(DeliverWarningSetting $setting)
    {
     return $this->item($setting->company(),new CompanyInfoTransformer());
    }
    
    public function includeWarninger(DeliverWarningSetting $setting)
    {
        if ($setting->warninger)
        {
            return $this->item($setting->warninger, new WarningerTransformer());
        }else
        {
            return  $this->null();
        }
    }
}
