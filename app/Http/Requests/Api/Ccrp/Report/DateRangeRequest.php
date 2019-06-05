<?php

namespace App\Http\Requests\Api\Ccrp\Report;

use App\Http\Requests\Api\Request;

class DateRangeRequest extends Request
{
    public function rules()
    {
        return [
            'start'=>'required|date_format:Y-m-d H:i:s',
            'end'=>'required|date_format:Y-m-d H:i:s',
        ];
    }
    public function attributes()
    {
        return [
            'start' => '开始时间',
            'end' => '结束时间',
        ];
    }
}
