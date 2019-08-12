<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\DeliverVehicle;
use League\Fractal\TransformerAbstract;

class DeliverVehicleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['company'];

    public function transform(DeliverVehicle $deliver)
    {
        $rs =  [
            'id' => $deliver->delivervehicle_id,
            'vehicle'=>$deliver->vehicle,
            'driver'=>$deliver->driver,
            'phone'=>$deliver->phone,
            'note'=>$deliver->note,
            'company_id'=>$deliver->company_id,
            'create_uid'=>$deliver->create_uid,
            'create_time'=>$deliver->create_time,
            'status'=>$deliver->status
        ];

        return $rs;
    }

    public function includeCompany(DeliverVehicle $deliver)
    {
        return $this->item($deliver->company(),new CompanyInfoTransformer());
    }

}
