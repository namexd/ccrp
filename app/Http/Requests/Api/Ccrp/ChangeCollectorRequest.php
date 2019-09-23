<?php

namespace App\Http\Requests\Api\Ccrp;


class ChangeCollectorRequest extends Request
{


    public function rules()
    {
        return [
            'collector_id'=>'required',
            'collector_name'=>'required',
            'cooler_id'=>'required',
            'category_id'=>'required',
            'supplier_id'=>'required',
            'supplier_product_model'=>'required',
            'supplier_collector_id'=>'required',
            'new_supplier_collector_id'=>'required',
            'change_note'=>'required',
        ];
    }
}
