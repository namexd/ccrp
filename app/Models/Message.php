<?php

namespace App\Models;

use function app\Utils\microservice_access_encode;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const FROM_TYPE=[
        'system',
        'user',
        'app',
        'user_group',
    ];

    const MESSAGE_TYPE=[
        '1'=>'话题回顾提醒',
        '2'=>'设备预警通知',
        '3'=>'工单处理通知',
        '4'=>'报警信息提醒',
        '5'=>'工单进度通知',
        '6'=>'开通成功通知',
    ];

    public function asyncSend($params)
    {
        $app = App::where('slug', 'ccrp')->first();
        $url = config('app.message_url');
        $access = microservice_access_encode($app->appkey, $app->appsecret,[]);
        $client = new Client();
        $res =  $client->request('POST', $url.'message', [
            'headers' => [
                'access' => $access,
            ],
            'form_params' =>$params
        ]);
    }

    public function asyncPush($params)
    {
        $app = App::where('slug', 'ccrp')->first();
        $url = config('app.pusher_url');
        $access = microservice_access_encode($app->appkey, $app->appsecret,[]);
        $client = new Client();
        $res =  $client->request('POST', $url.'send', [
            'headers' => [
                'access' => $access,
            ],
            'form_params' =>$params
        ]);
        \Log::info(json_decode($res->getBody()->getContents(),true));
    }
}
