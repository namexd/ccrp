<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors', 'statCooler'];

    public function transform(Cooler $cooler)
    {
        $arr = [
            'id' => $cooler->cooler_id,
            'cooler_sn' => $cooler->cooler_sn,
            'cooler_name' => $cooler->cooler_name,
            'brand' => $cooler->cooler_brand,
            'model' => $cooler->cooler_model,
            'size' => $cooler->cooler_size,
            'size2' => $cooler->cooler_size2,
            'company_id' => $cooler->company_id,
            'company' => $cooler->company->title??'',
            'created_at' => $cooler->install_time > 0 ? Carbon::createFromTimestamp($cooler->install_time)->toDateTimeString() : 0,
            'updated_at' => $cooler->update_time > 0 ? Carbon::createFromTimestamp($cooler->update_time)->toDateTimeString() : 0,
        ];
        if ($cooler->url)
        {
            $arr['url'] = $cooler->url;
        }
        $arr['image'] = $cooler->cooler_image??'';
        return $arr;
    }

    public function includeCollectors(Cooler $cooler)
    {
        return $this->collection($cooler->collectorsOnline, new CollectorIncludeTransformer());
    }

    public function includeStatCooler(Cooler $cooler)
    {
        $date = request()->get('date')??date('Y-m', strtotime('-1 Month'));
        return $this->collection($cooler->statCooler->where('month', $date), new StatCoolerTransformer());
    }
}