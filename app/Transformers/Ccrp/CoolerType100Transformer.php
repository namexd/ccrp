<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use League\Fractal\TransformerAbstract;

class CoolerType100Transformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors'];

    public function transform(Cooler $cooler)
    {
        $arr = [
            'id' => $cooler->cooler_id,
            'category' => $cooler->cooler_category ? $cooler->cooler_category->title : '',
            'cooler_name' => $cooler->cooler_name,
            'cooler_type' => $cooler->cooler_type ? Cooler::COOLER_TYPE[$cooler->cooler_type] : '未知',
            'status' => $cooler->status ? Cooler::$status[$cooler->status] : '未知',
        ];
        return $arr;
    }

    public function includeCollectors(Cooler $cooler)
    {
        return $this->collection($cooler->collectorsOnline, new CollectorIncludeTransformer());
    }

}