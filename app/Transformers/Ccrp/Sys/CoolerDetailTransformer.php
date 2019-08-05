<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\SysCoolerDetail;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerDetailTransformer extends TransformerAbstract
{
    public function transform(SysCoolerDetail $coolerType)
    {
        $arr=[
            'id'=>$coolerType->id,
            'name'=>$coolerType->name,
            'category'=>$coolerType->category,
            'slug'=>$coolerType->slug,
            'description'=>$coolerType->description,
            'note'=>$coolerType->note,
            'value'=>$coolerType->value,
            'created_at'=>Carbon::parse($coolerType->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($coolerType->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}