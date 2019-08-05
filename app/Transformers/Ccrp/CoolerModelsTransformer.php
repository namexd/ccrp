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
            'sys_brand_id' => $models->sys_brand_id,
            'user_model'=> $models -> user_model,
            'popularity'=>$models->popularity,
        ];
        return $arr;
    }
}