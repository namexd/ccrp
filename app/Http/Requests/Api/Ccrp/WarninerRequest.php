<?php


namespace App\Http\Requests\Api\Ccrp;


class WarninerRequest extends Request
{
    public function rules()
    {
        return [
            'warninger_name' => 'required',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}