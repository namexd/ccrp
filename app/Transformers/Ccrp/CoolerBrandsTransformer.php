<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\CoolerBrands;
use League\Fractal\TransformerAbstract;

class CoolerBrandsTransformer extends TransformerAbstract
{
    public function transform(CoolerBrands $brands)
    {
        $arr = [
            'id' => $brands -> id,
            'name'=> $brands -> sys_brand,
            'slug'=> $brands -> user_brand,
            'popularity'=>$brands->popularity,
            'is_approved' => $brands -> is_approved
        ];
        return $arr;
    }
}