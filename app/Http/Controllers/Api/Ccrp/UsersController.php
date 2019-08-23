<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\UsersLoginRequest;
use App\Models\Ccrp\User;

class UsersController extends Controller
{

    public function login(UsersLoginRequest $request)
    {
        $check = (new User)->checkPassword($request->username,$request->password);
        if($check)
        {
            return $this->response->array($check->toArray());
        }
        return $this->response->error('用户名或者密码错误',401);
    }
}
