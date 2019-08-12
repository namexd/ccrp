<?php

namespace App\Http\Requests\Api\Ccrp;


class DeliverOrderRequest extends Request
{


    public function rules()
    {
        return [
            'deliverorder'=>'required',
            'customer_name'=>'required',
            'collector_id'=>'required',
            'deliver_goods'=>'required',
            'delivervehicle'=>'required',
            'deliver'=>'required',
        ];
    }
}
