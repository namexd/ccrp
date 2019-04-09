<?php

namespace App\Transformers;

use App\Models\DataHistory;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorHistoryTransformer extends TransformerAbstract
{
    public function transform(DataHistory $history)
    {
        return [
            'data_id' => $history->data_id,
            'temp' => round($history->temp,1),
            'humi' =>round( $history->humi,1),
            'collect_time' =>  $history->collect_time??$history->sensor_collect_time,
            'system_time' =>  $history->system_time,
        ];
    }
}
