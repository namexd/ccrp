<?php

namespace App\Http\Requests\Api\Ccrp;

class WarningEventRequest extends Request
{
    public function rules()
    {
        return [
            'handler'=>'required|string|max:30',
            'handler_note'=>'required|string|max:100'
        ];
    }

    public function attributes()
    {
        return [
            'handler' => '处理人',
            'handler_note' => '处理备注',
        ];
    }
}
