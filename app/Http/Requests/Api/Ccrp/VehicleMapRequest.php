<?php

namespace App\Http\Requests\Api\Ccrp;


class VehicleMapRequest extends Request
{


    public function rules()
    {
        return [
            'vehicle'=>'required',
            'start'=>'required|date_format:Y-m-d',
            'end'=>'required|date_format:Y-m-d',
        ];
    }
}
