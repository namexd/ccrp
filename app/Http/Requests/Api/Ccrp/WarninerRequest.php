<?php


namespace App\Http\Requests\Api\Ccrp;


class WarninerRequest extends Request
{
    public function rules()
    {
        return [
            'warninger_name' => 'required',
            'warninger_type' => 'required',
            'warninger_body' => 'required',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}