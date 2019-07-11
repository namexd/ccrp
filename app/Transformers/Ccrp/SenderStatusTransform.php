<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\Dccharging;
use App\Models\Ccrp\SenderStatus;
use App\Models\CoolerCategory;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SenderStatusTransform extends TransformerAbstract
{
    public function transform(SenderStatus $senderStatus)
    {
        $arr = [
            'sender_volt' => $senderStatus->sender_volt,
            'sender_trans_time' => $senderStatus->sender_trans_time,
            'software_version' => $senderStatus->software_version,
            'ram_count' => $senderStatus->ram_count,
            'rom_count' => $senderStatus->rom_count,
            'csq' => csq($senderStatus->csq),
            'sender_volt' => $senderStatus->sender_volt,
            'system_time' => $senderStatus->system_time,

        ];
        return $arr;
    }
}

function csq($num)

{

    if (!$num) return '0%';

    return round((($num - 10) / 21) * 100, 0).'%';

}