<?php

namespace App\Models\Microservice;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class MicroserviceAPI
{
    public $access = '';
    private $api_server = '';

    public function __construct($access, $api_server = null)
    {
        $this->access = $access;
        $this->api_server = $api_server ?? '';
    }

    public function get($params)
    {
        $header = [
            'access' => $this->access,
        ];
        $options = [
            'query' => $params,
            'headers' => $header,
        ];

        return $options;
    }

    public function post($params)
    {
        $header = [
            'access' => $this->access,
        ];
        $options = [
            'form_params' => $params,
            'headers' => $header,
        ];
        return $options;
    }

    public function put($params)
    {
        $header = [
            'access' => $this->access,
        ];
        $options = [
            'form_params' => $params,
            'headers' => $header,
        ];
        return $options;
    }

    public function action($method, $function, $params)
    {
        switch ($method) {
            case 'GET':
                $options = $this->get($params);
                break;
            case 'POST':
                $options = $this->post($params);
                break;
            case 'PUT':
                $options = $this->put($params);
                break;
        }

        $client = new Client();
        try {
            $response = $client->$method($this->api_server . $function, $options);
            return $response->getBody()->getContents();
        } catch (RequestException $exception) {

            $content = $exception->getResponse()->getBody()->getContents();
            $content= json_decode($content,true);
            if(isset($content['status_code']) and $content['status_code']==422)
            {
                $content['message'] = '验证错误.';
            }

            return json_encode($content);
        } catch (Exception $exception) {
            return json_encode($exception);
        }
        return null;
    }

}
