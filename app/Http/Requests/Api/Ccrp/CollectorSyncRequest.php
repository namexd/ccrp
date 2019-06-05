<?php

namespace App\Http\Requests\Api\Ccrp;


class CollectorSyncRequest extends Request
{
    public function rules()
    {
        return [
            'sn'=>'required',
            'data_id'=>'required'
        ];
    }

    public function attributes()
    {
        return [
//            'collectors' => 'collectors 数组格式,[{"sn":"60201326","data_id":"1936109425"}]，目前可支持2个探头查询，',
        ];
    }
}
