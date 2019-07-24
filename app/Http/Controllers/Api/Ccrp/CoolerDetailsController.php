<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerDetail;
use App\Models\Ccrp\Sys\SysCoolerDetail;
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
        $details=$this->model->whereIn('cooler_id',$cooler_ids)->get();
        $fractal = new Manager();
        $sys_details=new Collection(SysCoolerDetail::all(),new CoolerDetailTransformer());
        $array = $fractal->createData($sys_details)->toArray();
        return $this->response->collection($details,new \App\Transformers\Ccrp\CoolerDetailTransformer())
            ->addMeta('sys_details',$array);
    }

    public function store()
    {
        $this->check();
        $result=[];
        $cooler_id = request()->get('cooler_id')??0;
        $cooler_sn = request()->get('cooler_sn')??0;
        if (!($cooler_id||$cooler_sn)){
            return $this->response->errorMethodNotAllowed('cooler_id或者cooler_sn不能为空');
        }
        if ($cooler_sn)
        {
            $cooler=Cooler::where('cooler_sn',$cooler_sn)->first();
            if (!$cooler)
            {
                return $this->response->errorMethodNotAllowed('冰箱不存在');
            }
            $cooler_id=$cooler->cooler_id;
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

    public function show($id)
    {
        $cooler=Cooler::where('cooler_id',$id)->orWhere('cooler_sn',$id)->first();
        if(!$cooler)
        {
            return $this->response->errorMethodNotAllowed('无法识别冰箱信息');
        }
        $details=$this->model->where('cooler_id',$cooler->cooler_id)->get();
        $fractal = new Manager();
        $sys_details=new Collection(SysCoolerDetail::all(),new CoolerDetailTransformer());
        $array = $fractal->createData($sys_details)->toArray();
        return $this->response->collection($details,new \App\Transformers\Ccrp\CoolerDetailTransformer())
            ->addMeta('sys_details',$array);
    }


}
