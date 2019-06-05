<?php

namespace App\Http\Controllers\Api;

use function App\Utils\microservice_access_encode;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;

    public function __construct()
    {
        $access = session()->get('access');
        $token = microservice_access_encode(env('MICROSERVICE_APPKEY'), env('MICROSERVICE_APPSECRET'), $access);
        dd($token);
    }
}
