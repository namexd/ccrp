<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\Dccharging;
use App\Models\CoolerCategory;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DcchargingTransformer extends TransformerAbstract
{
    public function transform(Dccharging $dccharging)
    {
        $arr = [
            'sender_volt' => $dccharging->sender_volt,
            'sender_sn2 ' => $dccharging->sender_sn,
            'sender_trans_time' => $dccharging->sender_trans_time,
            'system_time2' => $dccharging->system_time,
            'software_version' => $dccharging->software_version,
            'ram_count' => $dccharging->ram_count,
            'rom_count' => $dccharging->rom_count,
            'csq' => csq($dccharging->csq),
            'sender_volt' => $dccharging->sender_volt,
            'ischarging' => $dccharging->ischarging,

        ];
        return $arr;
    }
}

function csq($num)

{

    if (!$num) return '0%';

    return round((($num - 10) / 21) * 100, 0).'%';

}