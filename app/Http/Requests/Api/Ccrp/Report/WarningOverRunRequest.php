<?php

namespace App\Http\Requests\Api\Ccrp\Report;

use App\Http\Requests\Api\Ccrp\Request;

class WarningOverRunRequest extends Request
{
    public function rules()
    {
        return [
            'cooler_id'=>'required',
            'start'=>'required|date_format:Y-m-d H:i:s',
            'end'=>'required|date_format:Y-m-d H:i:s',
        ];
    }
    public function attributes()
    {
        return [
            'cooler_id' => '探头',
            'start' => '开始时间',
            'end' => '结束时间',
        ];
    }
}
