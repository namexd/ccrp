<?php

namespace App\Http\Requests\Api\Ccrp;

class WarningSettingRequest extends Request
{
    public function rules()
    {
        return [
            'warninger_id'=>'required',
            'collector_id'=>'required'
        ];
    }

}
