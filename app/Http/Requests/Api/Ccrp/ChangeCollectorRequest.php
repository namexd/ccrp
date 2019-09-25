<?php

namespace App\Http\Requests\Api\Ccrp;


class ChangeCollectorRequest extends Request
{


    public function rules()
    {
        return [
            'supplier_product_model'=>'required',
            'new_supplier_collector_id'=>'required',
            'change_note'=>'required',
        ];
    }
}
