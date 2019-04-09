<?php

namespace App\Http\Controllers\Api;

use App\Models\Cooler;
use App\Transformers\CoolerHistoryTransformer;
use App\Transformers\CoolerTransformer;
use Request;

class CoolersController extends Controller
{
    public function index()
    {
        $this->check();
        $coolers = Cooler::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc') ->orderBy('cooler_name','asc')->paginate($this->pagesize);
        return $this->response->paginator($coolers, new CoolerTransformer());
    }

    public function show($cooler)
    {
        $this->check();
        $cooler = Cooler::whereIn('company_id',$this->company_ids)->find($cooler);
        if($cooler)
        {
            return $this->response->item($cooler, new CoolerTransformer());
        }else{
            return $this->response->noContent();
        }
    }

    public function history($cooler)
    {
        $this->check();
        $start = request()->start ?? date('Y-m-d H:i:s',time()-4*3600);
        $end = request()->end ?? date('Y-m-d 23:59:59',strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        $cooler = Cooler::whereIn('company_id',$this->company_ids)->with('collectors')->find($cooler);
        if($cooler)
        {
            $data = $cooler->history($start_time,$end_time);
            return $this->response->item($data, new CoolerHistoryTransformer());
        }else{
            return $this->response->noContent();
        }
    }

}
