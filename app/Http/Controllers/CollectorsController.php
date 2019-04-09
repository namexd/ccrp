<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CollectorSyncRequest;
use App\Models\Collector;
use App\Models\Company;
use App\Models\DataHistory;
use App\Transformers\CollectorHistoryTransformer;
use App\Transformers\CollectorRealtimeTransformer;
use App\Transformers\CollectorTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CollectorsController extends Controller
{

    public function index()
    {
        $this->check();
        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate($this->pagesize);

        return $this->response->paginator($collectors, new CollectorTransformer());
    }
//
//    public function realtime()
//    {
//        $this->check();
//        $collectors = Collector::whereIn('company_id', $this->company_ids)->where('status', 1)
//            ->orderBy('company_id', 'asc')->orderBy('collector_name', 'asc')->paginate(10);
//        return $this->response->paginator($collectors, new CollectorRealtimeTransformer());
//    }

}
