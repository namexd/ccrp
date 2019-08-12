<?php

namespace App\Http\Requests\Api\Ccrp;


class DeliverWarningSettingRequest extends Request
{


    public function rules()
    {
        return [
            'setting_name'=>'required',
            'warninger_id'=>'required',
        ];
    }
}
