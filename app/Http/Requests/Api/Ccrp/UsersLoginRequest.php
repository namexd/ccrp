<?php

namespace App\Http\Requests\Api\Ccrp;


class UsersLoginRequest extends Request
{
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }

    public function attributes()
    {
        return [
        ];
    }
}
