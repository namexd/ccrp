<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerType100Transformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors'];

    public function transform(Cooler $cooler)
    {
        $arr = [
            'id' => $cooler->cooler_id,
            'category' => $cooler->category->title,
            'cooler_name' => $cooler->cooler_name,
            'cooler_type' => Cooler::COOLER_TYPE[$cooler->cooler_type],
            'status' =>Cooler::$status[$cooler->status],
        ];
        return $arr;
    }

    public function includeCollectors(Cooler $cooler)
    {
        return $this->collection($cooler->collectorsOnline, new CollectorIncludeTransformer());
    }

}