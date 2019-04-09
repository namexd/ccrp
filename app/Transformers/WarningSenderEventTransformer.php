<?php

namespace App\Transformers;

use App\Models\Sender;
use App\Models\WarningEvent;
use App\Models\WarningSenderEvent;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningSenderEventTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['options'];

    public function transform(WarningSenderEvent $event)
    {
        $sender = Sender::where('sender_id',$event->sender_id)->where('company_id',$event->company_id)->orderBy('status','asc')->first();
        $result = [
            'id' => $event->logid,
            'cooler_name' => '-',
            'device_name' => $sender->note??'',
            'device_sn' => $event->sender_id,
            'warning_type' => WarningEvent::断电预警,
            'warning_type_name' => WarningEvent::WARNING_TYPE[WarningEvent::断电预警],
            'warning_level' => $event->warning_level,
        ];

        $result['event_value'] = '';
        $result['range'] = [0,1];
        $result['handled'] = $event->handled;
        $result['handler'] = $event->handler;
        $result['handler_note'] = $event->handler_note;
        $result['handled_time'] = $event->handled_time ? Carbon::createFromTimestamp($event->handled_time)->toDateTimeString() : '';
        $result['company_id'] = $event->company_id;
        $result['created_at'] = $event->sensor_event_time ? Carbon::createFromTimestamp($event->sensor_event_time)->toDateTimeString() : '';
        return $result;
    }

    public function includeOptions(WarningSenderEvent $event)
    {
        return $this->collection($event->options, new WarningEventOptionTransformer());
    }
}
