<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\DataHistory;
use function App\Utils\format_value;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorHistoryTransformer extends TransformerAbstract
{
    public function transform(DataHistory $history)
    {
        return [
            'data_id' => $history->data_id,
            'temp' => format_value($history->temp),
            'humi' => format_value($history->humi),
            'collect_time' => Carbon::createFromTimestamp($history->collect_time)->toDateTimeString(),
            'system_time' => Carbon::createFromTimestamp($history->system_time)->toDateTimeString(),
        ];
    }
}