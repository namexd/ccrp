<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\CoolerPhoto;
use League\Fractal\TransformerAbstract;

class CoolerPhotoTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['sys_photo'];

    public function transform(CoolerPhoto $coolerPhoto)
    {
       return [
           'id' => $coolerPhoto->id,
           'cooler_id' => $coolerPhoto->cooler_id,
           'sys_id' => $coolerPhoto->sys_id,
           'value' => $coolerPhoto->value,
           'created_at' => $coolerPhoto->created_at->toDateTimeString(),
           'updated_at' => $coolerPhoto->updated_at->toDateTimeString()
       ];
    }
    public function includeSysPhoto(CoolerPhoto $coolerPhoto)
    {
        return $this->item($coolerPhoto->sys_photo,new \App\Transformers\Ccrp\Sys\CoolerPhotoTransformer());
    }
}