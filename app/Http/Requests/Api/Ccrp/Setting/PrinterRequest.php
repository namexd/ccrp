<?php

namespace App\Http\Requests\Api\Ccrp\Setting;

use App\Http\Requests\Api\Ccrp\Request;

class PrinterRequest extends Request
{
    public function rules()
    {
        return [
            'printer_sn'=>'required',
            'printer_key'=>'required',
            'printer_name'=>'required',
            'vehicle'=>'required',
        ];
    }
    public function attributes()
    {
        return [

        ];
    }
}
