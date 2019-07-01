<?php

namespace App\Http\Requests\Api\Ccrp\Report;

use App\Http\Requests\Api\Ccrp\Request;

class DateRangeRequest extends Request
{
    public function rules()
    {
        return [
            'start'=>'required',
            'end'=>'required',
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
