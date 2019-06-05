<?php

namespace App\Http\Requests\Api\Ccrp;


class PrinterRequest extends Request
{


    public function rules()
    {
        return [
            'printer_id'=>'required',
            'start'=>'required',
            'end'=>'required',
        ];
    }
}
