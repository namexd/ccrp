<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\CompanyPhysical;
use App\Models\Ccrp\DeliverLog;
use App\Models\Ccrp\PhysicalConfig;
use App\Transformers\Ccrp\CompanyPhysicalTransformer;

class CompanyPhysicalController extends Controller
{
    private $model;

    public function __construct(CompanyPhysical $companyPhysical)
    {
        $this->model = $companyPhysical;
    }

    public function index()
    {
        $this->check();
        $lists= $this->model->where('company_id',$this->company->id)->get();
        $result=[];
        foreach ($lists as $list)
        {
            $result[$list->count_time]['name']=$list->physical->name;
            $result[$list->count_time]['score']=$list->score;
            $result[$list->count_time]['detail']=$list->detail;
        }
        return $this->response->array($result);
    }


    public function show($time)
    {
        $this->check();
        $lists= $this->model->where('count_time',$time)->get();
        return $this->response->collection($lists,new CompanyPhysicalTransformer());

    }

    public function store()
    {
        $this->check();
        $id=request()->get('physical_id');
        $count_time=request()->get('count_time',time());
        $config=PhysicalConfig::query()->find($id);
        $function=$config->function;
        $result=$this->model->{$function}($this->company->id,$config);
        $result['company_id']=$this->company->id;
        $result['physical_id']=$id;
        $result['count_time']=$count_time;
        $this->model->create($result);
        return $this->response->array(['data'=>$result]);

    }

}
