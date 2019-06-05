<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array']
], function ($api) {
    // 版本
    $api->get('version', function () {
        return '1.0.19.4.25';
    });
    //测试：生成access发送请求
    $api->get('test/send', 'HelloController@send');

    //测试：生成access
    $api->get('test/access/{slug}', function ($slug) {
        $app = App\Models\App::where('slug', $slug)->first();
        $access = \App\Utils\microservice_access_encode($app->appkey, $app->appsecret, ['test' => 'hello word']);
        return $access;
    });

    $api->version('v1', [
        'middleware' => ['serializer:array', 'microservice_auth']
    ], function ($api) {
        //测试：接收请求，并解析access和附带info
        $api->get('test/receive', 'HelloController@receive');

    });

});