<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\WarningEvent;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningAllEventTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['options'];

    public function transform( $event)
    {
//        +"id": 90749
//    +"warning_type": "5"
//    +"collector_id": "0"
//    +"sender_id": "180302358"
//    +"temp_event": "-"
//    +"humi_event": "-"
//    +"event_time": 1544693818
//    +"handled": 0
//    +"handled_time": 0
//    +"handler": null
//    +"company_id": 2441
//    +"category_id": "0"
//    +"device_name": "团泊医院犬伤"
//    +"device_id": "中继主机：180302358"

        $result = [
            'id' => $event->id,
            'cooler_name' => $event->device_name,
            'collector_name' => $event->device_id,
            'collector_sn' => $event->sender_id,
            'warning_type' => $event->warning_type,
            'warning_type_name' => WarningEvent::WARNING_TYPE[$event->warning_type],
            'warning_level' => 1,
        ];
        switch ($event->warning_type) {
            case 1:
            case 2:
                $result['event_value'] = $event->temp_event;
                $result['range'] = [$event->temp_low, $event->temp_high];
                break;
            case 3:
            case 4:
                $result['event_value'] = $event->humi_event;
                $result['range'] = [$event->humi_low, $event->humi_high];
                break;
            case 5:
                $result['range'] = [1];
                $result['event_value'] = '1';
                break;
            case 6:
            case 7:
                $result['range'] = [$event->volt_low, $event - volt_high];
                $result['event_value'] = $event->vont_event;
                break;
        }
        $result['handled'] = $event->handled;
        $result['handler'] = $event->handler;
        $result['handler_note'] = $event->handler_note;
        $result['handled_time'] = $event->handled_time ? Carbon::createFromTimestamp($event->handled_time)->toDateTimeString() : '';
        $result['company_id'] = $event->company_id;
        $result['created_at'] = $event->event_time ? Carbon::createFromTimestamp($event->event_time)->toDateTimeString() : '';
        return $result;
    }

    public function includeOptions(WarningEvent $event)
    {
        return $this->collection($event->options, new WarningEventOptionTransformer());
    }
}