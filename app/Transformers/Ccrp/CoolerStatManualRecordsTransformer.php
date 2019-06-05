<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Cooler;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerStatManualRecordsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors'];

    public function transform(Cooler $cooler)
    {
        $collectors = $cooler->collectorsOnline;
        $data['cooler_id'] = $cooler->cooler_id;
        $data['cooler_sn'] = $cooler->cooler_sn;
        $data['collector_num'] = $cooler->collector_num;
        $data['cooler_name'] = $cooler->cooler_name;
        $data['company_id'] = $cooler->company_id;
        $data['cooler_type'] = $cooler->cooler_type;
        $temp_cool = [];
        $temp_cold = [];
        $need_note = false;
        foreach ($collectors as $collector) {
            if ($collector->temp_type == Collector::温区_冷藏) {
                if ($collector->refresh_time < (time() - Collector::离线时间)) {
                    $temp_cool[] = '离线';
                    $need_note = $need_note || true ;
                } else {
                    $temp_cool[] = $collector->temp;
                    $need_note = $need_note || false ;
                }
            } elseif ($collector->temp_type == Collector::温区_冷冻) {
                if ($collector->refresh_time < (time() - Collector::离线时间)) {
                    $temp_cold[] = '离线';
                    $need_note = $need_note || true ;
                } else {
                    $temp_cold[] = $collector->temp;
                    $need_note = $need_note || false ;
                }
            }
        }
        $temp_cool = implode('/', $temp_cool);
        $temp_cold = implode('/', $temp_cold);
        $data['temp_cool'] = $temp_cool==""?'/':$temp_cool;
        $data['temp_cold'] = $temp_cold==""?'/':$temp_cold;
        $collectors_error = $cooler->collectorsTempTypeError->count();
        $data['collector_type_error'] = $collectors_error;
        $data['need_note'] = (bool)$need_note;
        $data['sign_note'] = '';
        return $data;
    }


}