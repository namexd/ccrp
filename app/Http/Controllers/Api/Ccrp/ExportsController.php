<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\App;
use Illuminate\Http\Request;

class ExportsController extends Controller
{

    public function exportData(Request $request)
    {
        $url = App::where('program','microservice_export')->first()->api_url;
        $data=$request->all();
        $result=$this->microServiceClient('POST',$url,'store',$data);
        return $this->response->array($result);
    }

}
