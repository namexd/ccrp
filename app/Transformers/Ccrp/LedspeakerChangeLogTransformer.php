<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Ledspeaker;
use App\Models\Ccrp\LedspeakerLog;
use App\Models\Ccrp\Sender;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class LedspeakerChangeLogTransformer extends TransformerAbstract
{
    public function transform(LedspeakerLog $ledspeaker)
    {
        $rs =  [
            'id' => $ledspeaker->ledspeaker_id,
            'ledspeaker_id' => $ledspeaker->ledspeaker_id,
            'ledspeaker_name' => $ledspeaker->ledspeaker_name,
            'supplier_ledspeaker_id' => $ledspeaker->supplier_ledspeaker_id,
            'new_supplier_ledspeaker_id' => $ledspeaker->new_supplier_ledspeaker_id,
            'supplier_id' => $ledspeaker->supplier_id,
            'change_note' => $ledspeaker->change_note,
            'change_time' =>$ledspeaker->change_time?Carbon::createFromTimestamp($ledspeaker->change_time)->toDateTimeString():'',
            'change_option' => LedspeakerLog::CHANGE_OPTION[$ledspeaker->change_option]
        ];

        return $rs;
    }
}
