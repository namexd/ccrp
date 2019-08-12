<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\SysCoolerModel;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerModelTransformer extends TransformerAbstract
{
    protected $availableIncludes=['brand','type'];
    public function transform(SysCoolerModel $coolerModel)
    {
        $arr=[
            'id'=>$coolerModel->id,
            'name'=>$coolerModel->name,
            'type_id'=>$coolerModel->type_id,
            'brand_id'=>$coolerModel->brand_id,
            'specifications'=>$coolerModel->specifications,
            'slug'=>$coolerModel->slug,
            'description'=>$coolerModel->description,
            'weight'=>$coolerModel->weight,
            'temperature'=>$coolerModel->temperature,
            'power'=>$coolerModel->power,
            'body_type'=>$coolerModel->body_type,
            'cool_volume'=>$coolerModel->cool_volume,
            'cold_volume'=>$coolerModel->cold_volume,
            'whole_volume'=>$coolerModel->whole_volume,
            'is_medical'=>$coolerModel->is_medical,
            'medical_licence'=>$coolerModel->medical_licence,
            'warmarea_count'=>$coolerModel->warmarea_count,
            'comment'=>$coolerModel->comment,
            'popularity'=>$coolerModel->popularity,
            'created_at'=>Carbon::parse($coolerModel->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($coolerModel->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }

    public function includeType(SysCoolerModel $coolerModel)
    {
        return $this->item($coolerModel->type,new CoolerTypeTransformer());
    }
    public function includeBrand(SysCoolerModel $coolerModel)
    {
        return $this->item($coolerModel->brand,new CoolerBrandTransformer());
    }
}