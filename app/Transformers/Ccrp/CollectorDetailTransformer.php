<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use function App\Utils\format_value;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CollectorDetailTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['warningSetting','cooler'];
    public function transform(Collector $collector)
    {
        return [
            'id' => $collector->collector_id,
            'sn' => $collector->supplier_collector_id,
            'name' => $collector->collector_name,
            'temp' => format_value($collector->temp,'-'),
            'humi' => format_value($collector->humi),
            'volt' => ($collector->volt > 0 ) ? round($collector->volt, 1):null,
            'rssi' => ($collector->rssi > -999) ?round($collector->rssi, 1):null,
            'offline_check' => (boolean)$collector->offline_check,
            'offline_span' => $collector->offline_span,
            'cooler_id' => $collector->cooler_id,
            'cooler_name' => $collector->cooler_name,
            'company_id' => $collector->company_id,
            'company' => $collector->company->title,
            'refresh_time' => $collector->refresh_time>0?Carbon::createFromTimestamp($collector->refresh_time)->toDateTimeString():0,
            'created_at' => $collector->created_at?$collector->created_at->toDateTimeString():'',
            'updated_at' => $collector->updated_at?$collector->updated_at->toDateTimeString():'',
        ];
    }

    public function includeWarningSetting(Collector $collector)
    {
        if($collector->warningSetting)
        {
            return $this->item($collector->warningSetting, new WarningSettingTransformer());
        }else{
            return new Item([],function (){
                return [];
            });
        }
    }

    public function includeCooler(Collector $collector)
    {
        return $this->item($collector->cooler,new CoolerTransformer());
    }
}
