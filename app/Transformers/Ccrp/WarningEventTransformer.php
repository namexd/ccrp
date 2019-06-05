<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSetting;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningEventTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['options'];

    public function transform(WarningEvent $event)
    {
        $result = [
            'id' => $event->id,
            'cooler_name' => $event->collector->cooler_name,
            'device_name' => $event->collector->collector_name,
            'device_sn' => $event->collector->supplier_collector_id,
            'warning_type' => $event->warning_type,
            'warning_type_name' => WarningEvent::WARNING_TYPE[$event->warning_type],
            'warning_level' => $event->warning_level,
        ];
        switch ($event->warning_type) {
            case 1:
            case 2:
                $result['event_value'] = round($event->temp_event,1);
                $result['range'] = [$event->temp_low, $event->temp_high];
                break;
            case 3:
            case 4:
                $result['event_value'] = round($event->humi_event,1);
                $result['range'] = [$event->humi_low, $event->humi_high];
                break;
            case 5:
                $result['range'] = [1];
                $result['event_value'] = '1';
                break;
            case 6:
            case 7:
            $result['event_value'] = round($event->vont_event);
            $result['range'] = [$event->volt_low, $event - volt_high];
                break;
        }
        $result['handled'] = $event->handled;
        $result['handler'] = $event->handler;
        $result['handler_note'] = $event->handler_note;
        $result['handled_time'] = $event->handled_time ? Carbon::createFromTimestamp($event->handled_time)->toDateTimeString() : '';
        $result['company_id'] = $event->company_id;
        $result['created_at'] = $event->warning_event_time ? Carbon::createFromTimestamp($event->warning_event_time)->toDateTimeString() : '';
        return $result;
    }

    public function includeOptions(WarningEvent $event)
    {
        return $this->collection($event->options, new WarningEventOptionTransformer());
    }
}