<?php

namespace App\Http\Requests\Api\Ccrp\Setting;

use App\Http\Requests\Api\Ccrp\Request;

class CompanySettingRequest extends Request
{
    public function rules()
    {
        return [
            'title'=>'required',
            'office_title'=>'required',
            'short_title'=>'required',
            'username'=>'required',
            'password'=>'required',
        ];
    }
    public function attributes()
    {
        return [

        ];
    }
}
