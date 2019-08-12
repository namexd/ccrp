<?php

namespace App\Http\Requests\Api\Ccrp;


class DeliverRequest extends Request
{


    public function rules()
    {
        return [
            'deliver'=>'required',
            'phone'=>'required',
        ];
    }
}
