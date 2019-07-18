<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\CoolerPhoto;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerPhotoTransformer extends TransformerAbstract
{
    public function transform(CoolerPhoto $coolerPhoto)
    {
        $arr=[
            'id'=>$coolerPhoto->id,
            'name'=>$coolerPhoto->name,
            'category'=>$coolerPhoto->category,
            'slug'=>$coolerPhoto->slug,
            'value'=>$coolerPhoto->value,
            'description'=>$coolerPhoto->description,
            'note'=>$coolerPhoto->note,
            'created_at'=>Carbon::parse($coolerPhoto->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($coolerPhoto->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}