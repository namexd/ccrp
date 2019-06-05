<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorDetailTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['warningSetting'];
    public function transform(Collector $collector)
    {
        return [
            'id' => $collector->collector_id,
            'sn' => $collector->supplier_collector_id,
            'name' => $collector->collector_name,
            'temp' => round($collector->temp, 1),
            'humi' => round($collector->humi, 1),
            'volt' => round($collector->volt, 1),
            'rssi' => round($collector->rssi, 1),
            'offline_check' => (boolean)$collector->offline_check,
            'offline_span' => $collector->offline_span,
            'cooler_id' => $collector->cooler_id,
            'cooler_name' => $collector->cooler_name,
            'company_id' => $collector->company_id,
            'company' => $collector->company->title,
            'refresh_time' => $collector->refresh_time>0?Carbon::createFromTimestamp($collector->refresh_time)->toDateTimeString():0,
            'created_at' => $collector->install_time>0?Carbon::createFromTimestamp($collector->install_time)->toDateTimeString():0,
            'updated_at' => $collector->update_time>0?Carbon::createFromTimestamp($collector->update_time)->toDateTimeString():0,
        ];
    }

    public function includeWarningSetting(Collector $collector)
    {
        if($collector->warningSetting)
        {
            return $this->item($collector->warningSetting, new WarningSettingTransformer());
        }else{
            return null;
        }
    }
}