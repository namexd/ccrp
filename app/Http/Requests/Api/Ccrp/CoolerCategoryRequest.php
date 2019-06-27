<?php

namespace App\Http\Requests\Api\Ccrp;


class CoolerCategoryRequest extends Request
{


    public function rules()
    {
        return [
            'title'=>'required',
        ];
    }
}
