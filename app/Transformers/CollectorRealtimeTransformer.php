<?php

namespace App\Transformers;

use App\Models\Collector;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorRealtimeTransformer extends TransformerAbstract
{
    public function transform(Collector $collector)
    {
        return [
            'collector_id' => $collector->collector_id,
            'temp' => round($collector->temp,1),
            'humi' => round($collector->humi,1),
            'refresh_time' => $collector->refresh_time
        ];
    }
}
