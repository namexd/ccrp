<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\PhysicalConfig;
use League\Fractal\TransformerAbstract;

class PhysicalConfigTransformer extends TransformerAbstract
{
    public function transform(PhysicalConfig $config)
    {
        $rs =  [
            'id' => $config->id,
            'name'=>$config->name,
            'weight'=>$config->weight,
            'description'=>$config->description,
            'note'=>$config->note,
            'created_at' => $config->created_at->toDateTimeString(),
            'updated_at' => $config->updated_at->toDateTimeString()
        ];

        return $rs;
    }
}
