<?php

namespace App\Http\Requests\Api\Ccrp;

class StatManualRecordRequest extends Request
{
    public function rules()
    {
        return [
            'records' => 'required',
            'sign_image_uniqid' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'records' => '温度数据',
            'sign_image_uniqid' => '手写签名',
        ];
    }
}
