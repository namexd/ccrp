<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use function App\Utils\format_value;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorRealtimeTransformer extends TransformerAbstract
{
    public function transform(Collector $collector)
    {
        $rs = [
            'id' => $collector->collector_id,
            'sn' => $collector->supplier_collector_id,
            'name' => $collector->collector_name,
            'cooler_id' => $collector->cooler_id,
            'cooler_name' => $collector->cooler_name,
            'company_id' => $collector->company_id,
            'company' => $collector->company->title,
            'temp' => format_value($collector->temp,'-'),
            'humi' => format_value($collector->humi,'-'),
            'refresh_time' =>$collector->refresh_time?Carbon::createFromTimestamp($collector->refresh_time)->toDateTimeString():'',
        ];
        $rs['unnormal_status'] = $collector->unnormal_status;
        $rs['warning_setting_temp_range'] = $collector->warning_setting_temp_range;
        return $rs;
    }
}
