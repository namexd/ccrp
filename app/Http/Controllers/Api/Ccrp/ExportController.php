<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\App;
use function App\Utils\microservice_access_encode;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ExportController extends Controller
{
    public $slug = 'microservice_export';

    public function callback()
    {
        $access=session()->get('access');
        $app = App::where('program', $this->slug)->first();
        $app2=App::find($access['app']['id']);
        $access2=microservice_access_encode($app2->appkey,$app2->appsecret,$access['info']);
        $params=request()->all();
        $params['access']=$access2;
        $header = [
            'access' => $access2
        ];
        $options = [
            'form_params' => $params,
            'headers' => $header,
        ];
        $client = new Client();
        try {
            $response = $client->request('POST', $app->api_url.'store', $options);
            return $this->response->array(json_decode($response->getBody()->getContents(),true));
        } catch (RequestException $exception) {
            return $exception->getMessage();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
        return null;
    }

}
