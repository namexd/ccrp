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
            'sys_model'=> $models -> sys_model,
            'user_model'=> $models -> user_model,
            'popularity'=>$models->popularity,
            'is_approved' => $models -> is_approved
        ];
        return $arr;
    }
}