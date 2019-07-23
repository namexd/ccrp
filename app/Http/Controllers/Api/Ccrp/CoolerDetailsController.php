<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\Setting\CoolerAddRequest;
use App\Models\Ccrp\CompanyDetail;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerDetail;
use App\Models\Ccrp\Sys\SysCompanyDetail;
use App\Models\Ccrp\Sys\SysCoolerDetail;
use App\Transformers\Ccrp\CompanyDetailTableTransformer;
use App\Transformers\Ccrp\CompanyListTransformer;
use App\Transformers\Ccrp\Sys\CompanyDetailTransformer;
use App\Transformers\Ccrp\Sys\CoolerDetailTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

class CoolerDetailsController extends Controller
{
    public $model;

    public function __construct(CoolerDetail $coolerDetail)
    {
        $this->model = $coolerDetail;
    }

    public function index(Request $request)
    {
        $this->check();
        $cooler_ids=Cooler::whereIn('company_id',$this->company_ids)->pluck('cooler_id');
        $details=$this->model->whereIn('cooler_id',$cooler_ids)->paginate($request->pagesize??$this->pagesize);
        $fractal = new Manager();
        $sys_details=new Collection(SysCoolerDetail::all(),new CoolerDetailTransformer());
        $array = $fractal->createData($sys_details)->toArray();
        return $this->response->paginator($details,new \App\Transformers\Ccrp\CoolerDetailTransformer())
            ->addMeta('sys_details',$array);
    }

    public function store()
    {
        $this->check();
        $result=[];
        $cooler_id=request()->get('cooler_id');
        if (!$cooler_id)
        {
            return $this->response->errorMethodNotAllowed('cooler_id不能为空');
        }
        $details=request()->get('details');
        $details=is_array($details)?$details:json_decode($details,true);
        foreach ($details as $detail)
        {
            $search=[
                'cooler_id'=>$cooler_id,
                'sys_id'=>$detail['sys_id'],
            ];
            $attribute=['value'=>$detail['value']];
            $result[]=$this->model->updateOrCreate($search,$attribute);
        }
        return $this->response->array($result);
    }


}
