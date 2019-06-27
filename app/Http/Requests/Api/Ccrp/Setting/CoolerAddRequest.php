<?php

namespace App\Http\Requests\Api\Ccrp\Setting;

use App\Http\Requests\Api\Ccrp\Request;

class CoolerAddRequest extends Request
{
    public function rules()
    {
        return [
            'cooler_name'=>'required',
            'cooler_brand'=>'required',
            'cooler_model'=>'required',
            'cooler_sn'=>'required',
        ];
    }
    public function attributes()
    {
        return [

        ];
    }
}
