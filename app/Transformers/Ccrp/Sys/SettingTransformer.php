<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\Setting;
use League\Fractal\TransformerAbstract;

class SettingTransformer extends TransformerAbstract
{
    public function transform(Setting $item)
    {
        $arr = [
            'category' => Setting::CATEGORIES[$item->category],
            'name' => $item->name,
            'value' => $item->diy_value??$item->value,
        ];
        if ($item->type == 'select') {
            $options = json_decode($item->options, true);
            $arr['value'] = $options[$item->value];
        }
        if ($item->tip) {
            $arr['tip'] = $item->tip;
        }
        if(request()->get('with'))
        {
            unset($arr['category']);
        }
        return $arr;
    }
}
