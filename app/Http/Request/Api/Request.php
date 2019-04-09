<?php

namespace App\Http\Requests\Api;


class Request extends \Laravel\Lumen\Http\Request
{
    public function authorize()
    {
        return true;
    }

}
