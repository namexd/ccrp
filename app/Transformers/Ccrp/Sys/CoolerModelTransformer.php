<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\CoolerModel;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerModelTransformer extends TransformerAbstract
{
    protected $availableIncludes=['brand','type'];
    public function transform(CoolerModel $coolerModel)
    {
        $arr=[
            'id'=>$coolerModel->id,
            'name'=>$coolerModel->name,
            'slug'=>$coolerModel->slug,
            'description'=>$coolerModel->slug,
            'cool_volume'=>$coolerModel->cool_volume,
            'cold_volume'=>$coolerModel->cold_volume,
            'whole_volume'=>$coolerModel->whole_volume,
            'is_medical'=>$coolerModel->is_medical,
            'created_at'=>Carbon::parse($coolerModel->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($coolerModel->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }

    public function includeType(CoolerModel $coolerModel)
    {
        return $this->item($coolerModel->type,new CoolerTypeTransformer());
    }
    public function includeBrand(CoolerModel $coolerModel)
    {
        return $this->item($coolerModel->brand,new CoolerBrandTransformer());
    }
}