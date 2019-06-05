<?php

namespace App\Http\Controllers\Api;


use App\Models\App;
use function App\Utils\microservice_access_encode;
use GuzzleHttp\Client;

class HelloController extends Controller
{
    public function receive()
    {
        $response['access'] = session()->get('access');
        $response['hello'] = 'Hello word!!!';
        //dingo response
        return $this->response->array($response);
    }

    public function send()
    {
        $appkey = 'CCRPSOL';
        $appsecret = '5FC33228E55651487B9';
        $url = 'microservice.test';
        $access = microservice_access_encode($appkey,$appsecret,['test'=>'hello ,im ccrp requester']);
        $client = new Client();
        $res =  $client->request('GET', $url.'/api/test/receive', [
            'headers' => [
                'access' => $access,
            ]
        ]);
        $respone['data'] = json_decode($res->getBody()->getContents(),true);
        $respone['_tips'] = '请求：'.$url.'成功啦！';
        return $this->response->array($respone);
    }
}