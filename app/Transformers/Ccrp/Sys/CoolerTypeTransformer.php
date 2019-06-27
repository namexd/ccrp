<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\CoolerType;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerTypeTransformer extends TransformerAbstract
{
    public function transform(CoolerType $coolerType)
    {
        $arr=[
            'id'=>$coolerType->id,
            'name'=>$coolerType->name,
            'category'=>$coolerType->category,
            'slug'=>$coolerType->slug,
            'description'=>$coolerType->description,
            'note'=>$coolerType->note,
            'created_at'=>Carbon::parse($coolerType->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($coolerType->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}