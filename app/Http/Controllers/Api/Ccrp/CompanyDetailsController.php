<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\CompanyDetail;
use App\Models\Ccrp\Sys\SysCompanyDetail;
use App\Transformers\Ccrp\CompanyDetailTableTransformer;
use App\Transformers\Ccrp\Sys\CompanyDetailTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

class CompanyDetailsController extends Controller
{
    public $model;

    public function __construct(CompanyDetail $companyDetail)
    {
        $this->model = $companyDetail;
    }

    public function index(Request $request)
    {
        $this->check();
        $details=$this->model->where('company_id',$this->company->id)->paginate($request->pagesize??$this->pagesize);
        $fractal = new Manager();
        $sys_details=new Collection(SysCompanyDetail::all(),new CompanyDetailTransformer());
        $array = $fractal->createData($sys_details)->toArray();
        return $this->response->paginator($details,new CompanyDetailTableTransformer())
            ->addMeta('sys_details',$array);
    }

}
