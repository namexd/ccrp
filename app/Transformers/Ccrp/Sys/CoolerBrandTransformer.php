<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\SysCoolerBrand;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerBrandTransformer extends TransformerAbstract
{
    public function transform(SysCoolerBrand $brand)
    {
        $arr=[
            'id'=>$brand->id,
            'name'=>$brand->name,
            'slug'=>$brand->slug,
            'comporation'=>$brand->comporation,
            'has_medical'=>$brand->has_medical,
            'popularity' => $brand->popularity,
            'created_at'=>Carbon::parse($brand->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($brand->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}