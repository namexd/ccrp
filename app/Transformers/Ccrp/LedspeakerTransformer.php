<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Ledspeaker;
use App\Models\Ccrp\Sender;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class LedspeakerTransformer extends TransformerAbstract
{
    protected $availableIncludes=['collectors','senders'];
    public function transform(Ledspeaker $ledspeaker)
    {
        $rs =  [
            'id' => $ledspeaker->ledspeaker_id,
            'ledspeaker_name' => $ledspeaker->ledspeaker_name,
            'supplier_ledspeaker_id' => $ledspeaker->supplier_ledspeaker_id,
            'supplier_model' => $ledspeaker->supplier_model,
            'install_time' =>$ledspeaker->install_time?Carbon::createFromTimestamp($ledspeaker->install_time)->toDateTimeString():'',
            'simcard' => $ledspeaker->simcard,
            'refresh_time' => $ledspeaker->refresh_time,
            'collector_num' =>  $ledspeaker->collector_num,
            'module' => Ledspeaker::LEDSPEAKER_MODULE[$ledspeaker->module],
            'status' =>$ledspeaker->status,
            'collectors' =>$ledspeaker->collectors(),
            'senders' =>$ledspeaker->senders(),
        ];

        return $rs;
    }
    public function includeCollectors(Ledspeaker $ledspeaker)
    {
        return $this->collection($ledspeaker->collectors(),new CollectorTransformer());
    }
    public function includeSenders(Ledspeaker $ledspeaker)
    {
        return $this->collection($ledspeaker->senders(),new SenderTransformer());
    }
}
