<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use function App\Utils\format_value;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerHistoryTransformer extends TransformerAbstract
{

    protected $start_time,$end_time;


    public function transform(Cooler $cooler)
    {
        $coolerdata = [
            'id' => $cooler->cooler_id,
            'cooler_sn' => $cooler->cooler_sn,
            'cooler_name' => $cooler->cooler_name,
            'brand' => $cooler->cooler_brand,
            'model' => $cooler->cooler_model,
            'size' => $cooler->cooler_size,
            'size2' => $cooler->cooler_size2,
            'company_id' => $cooler->company_id,
            'company' => $cooler->company->title,
            'created_at' => $cooler->install_time > 0 ? Carbon::createFromTimestamp($cooler->install_time)->toDateTimeString() : 0,
            'updated_at' => $cooler->update_time > 0 ? Carbon::createFromTimestamp($cooler->update_time)->toDateTimeString() : 0,
        ];
        foreach ($cooler->collectors as $collector) {
            $collectordata = [
                'id' => $collector->collector_id,
                'sn' => $collector->supplier_collector_id,
                'name' => $collector->collector_name,
                'cooler_id' => $collector->cooler_id,
                'cooler_name' => $collector->cooler_name,
                'company_id' => $collector->company_id,
                'company' => $collector->company->title,
                'created_at' => $collector->install_time > 0 ? Carbon::createFromTimestamp($collector->install_time)->toDateTimeString() : 0,
                'updated_at' => $collector->update_time > 0 ? Carbon::createFromTimestamp($collector->update_time)->toDateTimeString() : 0,
            ];
            if($collector->warningSetting)
            {
                $collectordata['warningSetting'] = [
                    'id'=>$collector->warningSetting->id,
                    'temp_low'=>$collector->warningSetting->temp_low,
                    'temp_high'=>$collector->warningSetting->temp_high,
                    'temp_warning'=>$collector->warningSetting->temp_warning,
                    'status'=>$collector->warningSetting->status,
                ];
            }

            if($collector->history)
            {
                foreach ($collector->history as $item) {
                    $collectordata['history'][] = [
                        'data_id' => $item->data_id,
                        'temp' => format_value($item->temp),
                        'humi' => format_value($item->humi),
                        'collect_time' => Carbon::createFromTimestamp($item->collect_time)->toDateTimeString(),
                        'system_time' => Carbon::createFromTimestamp($item->system_time)->toDateTimeString(),
                    ];
                }
            }
            $coolerdata['collectors'][] = $collectordata;
        }
        return $coolerdata;
    }


}