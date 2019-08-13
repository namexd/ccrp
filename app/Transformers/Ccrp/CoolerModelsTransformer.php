<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\CoolerModels;
use League\Fractal\TransformerAbstract;

class CoolerModelsTransformer extends TransformerAbstract
{
    public function transform(CoolerModels $models)
    {
        $arr = [
            'id' => $models -> id,
            'brand_id' => $models->sys_brand_id,
            'name'=> $models -> user_model,
            'popularity'=>$models->popularity,
        ];
        return $arr;
    }
}