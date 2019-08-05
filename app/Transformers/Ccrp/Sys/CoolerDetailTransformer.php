<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\SysCoolerDetail;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerDetailTransformer extends TransformerAbstract
{
    public function transform(SysCoolerDetail $coolerDetail)
    {
        $arr=[
            'id'=>$coolerDetail->id,
            'name'=>$coolerDetail->name,
            'category'=>$coolerDetail->category,
            'slug'=>$coolerDetail->slug,
            'description'=>$coolerDetail->description,
            'note'=>$coolerDetail->note,
            'sys_value'=>$coolerDetail->value,
            'created_at'=>Carbon::parse($coolerDetail->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($coolerDetail->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}