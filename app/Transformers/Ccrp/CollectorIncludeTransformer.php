<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CollectorIncludeTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['warningSetting'];
    public function transform(Collector $collector)
    {
        return [
            'id' => $collector->collector_id,
            'sn' => $collector->supplier_collector_id,
            'name' => $collector->collector_name,
            'temp' =>$collector->temp,
            'humi' =>$collector->humi,
            'refresh_time' => Carbon::createFromTimestamp($collector->refresh_time)->toDateTimeString(),
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
}