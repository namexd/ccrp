<?php

namespace App\Http\Requests\Api\Ccrp;


class DeliverVehicleRequest extends Request
{


    public function rules()
    {
        return [
            'vehicle'=>'required',
            'driver'=>'required',
            'phone'=>'required'
        ];
    }
}
