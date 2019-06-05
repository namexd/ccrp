<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CollectorTransformer extends TransformerAbstract
{
    public function transform(Collector $collector)
    {
        return [
            'id' => $collector->collector_id,
            'sn' => $collector->supplier_collector_id,
            'name' => $collector->collector_name,
            'cooler_id' => $collector->cooler_id,
            'cooler_name' => $collector->cooler_name,
            'company_id' => $collector->company_id,
            'company' => $collector->company->title,
            'created_at' => $collector->install_time>0?Carbon::createFromTimestamp($collector->install_time)->toDateTimeString():0,
            'updated_at' => $collector->update_time>0?Carbon::createFromTimestamp($collector->update_time)->toDateTimeString():0,
        ];
    }
}