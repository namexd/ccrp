<?php

namespace App\Http\Requests\Api\Ccrp;


class CollectorRequest extends Request
{


    public function rules()
    {
        return [
            'collector_name'=>'required',
            'cooler_id'=>'required',
            'supplier_product_model'=>'required',
            'supplier_collector_id'=>'required',
        ];
    }
}
