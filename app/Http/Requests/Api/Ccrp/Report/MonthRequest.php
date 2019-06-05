<?php

namespace App\Http\Requests\Api\Ccrp\Report;

use App\Http\Requests\Api\Request;

class MonthRequest extends Request
{
    public function rules()
    {
        return [
            'month'=>'required|date_format:Y-m'
        ];
    }
    public function attributes()
    {
        return [
            'month' => '月份',
        ];
    }
}
