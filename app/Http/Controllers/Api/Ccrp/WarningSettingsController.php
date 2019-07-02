<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\WarningEventRequest;
use App\Models\Ccrp\Warninger;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
use App\Models\Ccrp\WarningSetting;
use App\Transformers\Ccrp\WarningAllEventTransformer;
use App\Transformers\Ccrp\WarningEventTransformer;
use App\Transformers\Ccrp\WarningSenderEventTransformer;
use App\Transformers\Ccrp\WarningSettingTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

class WarningSettingsController extends Controller
{
    private $model;

    public function __construct(WarningSetting $warningSetting)
    {
        $this->model = $warningSetting;
    }

    public function index()
    {
        $this->check();
        if ($keyword=request()->get('keywaord'))
        {
            $this->model=$this->model->whereHas('collector',function ($query) use ($keyword){
               $query->where('collector_name',$keyword)->whereOr('supplier_collector_id',$keyword);
            });
        }
        $warnings=$this->model->whereIn('company_id',$this->company_ids)->where('status',1)->paginate($this->pagesize);
        return $this->response->paginator($warnings,new WarningSettingTransformer());
    }

    public function show($id)
    {
        $this->check();

    }

    public function update($id)
    {
        $this->check();

    }

    public function store()
    {
        $this->check();

    }

}
