<?php

namespace App\Http\Requests\Api\Ccrp\Setting;

use App\Http\Requests\Api\Ccrp\Request;

class CoolerStatusRequest extends Request
{
    public function rules()
    {
        return [
            'note'=>'required',
            'status'=>'required',
        ];
    }
    public function attributes()
    {
        return [

        ];
    }
}
