<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\CompanyDetail;
use App\Models\Ccrp\Sys\SysCompanyDetail;
use App\Transformers\Ccrp\CompanyDetailTableTransformer;
use App\Transformers\Ccrp\CompanyListTransformer;
use App\Transformers\Ccrp\Sys\CompanyDetailTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

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
        $details=$this->model->whereIn('company_id',$this->company_ids)->get();
        $fractal = new Manager();
        $sys_details=new Collection(SysCompanyDetail::all(),new CompanyDetailTransformer());
        $array = $fractal->createData($sys_details)->toArray();
        $company=new Item($this->company,new CompanyListTransformer());
        $array2= $fractal->createData($company)->toArray();
        return $this->response->collection($details,new CompanyDetailTableTransformer())
            ->addMeta('sys_details',$array)
            ->addMeta('company',$array2);
    }

    public function store()
    {
        $this->check();
        $result=[];
        $company_id=$this->company->id;
        $details=request()->get('details');
        $details=is_array($details)?$details:json_decode($details,true);
        foreach ($details as $detail)
        {
            $search=[
                'company_id'=>$company_id,
                'sys_id'=>$detail['sys_id'],
            ];
            $attribute=['value'=>$detail['value']];
            $result[]=$this->model->updateOrCreate($search,$attribute);
        }
        return $this->response->array($result);
    }


}
