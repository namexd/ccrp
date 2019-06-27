<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\Setting\CoolerAddRequest;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Sys\CoolerType;
use App\Transformers\Ccrp\CoolerHistoryTransformer;
use App\Transformers\Ccrp\CoolerTransformer;
use App\Transformers\Ccrp\CoolerType100Transformer;
use App\Transformers\Ccrp\Sys\CoolerTypeTransformer;
use Illuminate\Http\Request;

;

class CoolersController extends Controller
{
    private $cooler;

    public function __construct(Cooler $cooler)
    {

        $this->cooler = $cooler;
    }

    public function index()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('status', 1);
        if (request()->get('has_collector')) {
            $coolers = $coolers->where('collector_num', '>', 0);
        }
        $coolers = $coolers->with('company')
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->paginate($this->pagesize);
        return $this->response->paginator($coolers, new CoolerTransformer());
    }

    public function all()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->get();
        return $this->response->collection($coolers, new CoolerTransformer());
    }

    public function show($cooler)
    {
        $this->check();
        $cooler = $this->cooler->whereIn('company_id', $this->company_ids)->find($cooler);
        if ($cooler) {
            return $this->response->item($cooler, new CoolerTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function history($cooler)
    {
        $this->check();
        $start = request()->start ?? date('Y-m-d H:i:s', time() - 4 * 3600);
        $end = request()->end ?? date('Y-m-d 23:59:59', strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        $cooler = $this->cooler->whereIn('company_id', $this->company_ids)->with('collectors')->find($cooler);
        if ($cooler) {
            $data = $cooler->history($start_time, $end_time);
            return $this->response->item($data, new CoolerHistoryTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function coolerType100()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('cooler_type', 100);
        $coolers = $coolers->with(['category', 'collectors'])
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->paginate($this->pagesize);
        return $this->response->paginator($coolers, new CoolerType100Transformer());
    }

    public function store(CoolerAddRequest $request)
    {
        $this->check();
        $request['company_id']=$this->company->id;
        $result = $this->cooler->addCooler($request->all());
        return $this->response->item($result, new CoolerTransformer)->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->check();
        $cooler=$this->cooler->find($id);
        $cooler->fill($request->all());
        $cooler->save();
        return $this->response->item($cooler,new CoolerTransformer());
    }

    public function coolerType()
    {
        return $this->response->collection(CoolerType::all(),new CoolerTypeTransformer());
    }

}
