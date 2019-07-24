<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\Setting\CoolerAddRequest;
use App\Models\Ccrp\CompanyDetail;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerDetail;
use App\Models\Ccrp\CoolerPhoto;
use App\Models\Ccrp\Sys\SysCompanyDetail;
use App\Models\Ccrp\Sys\SysCoolerDetail;
use App\Models\Ccrp\Sys\SysCoolerPhoto;
use App\Transformers\Ccrp\CompanyDetailTableTransformer;
use App\Transformers\Ccrp\CompanyListTransformer;
use App\Transformers\Ccrp\Sys\CompanyDetailTransformer;
use App\Transformers\Ccrp\Sys\CoolerDetailTransformer;
use App\Transformers\Ccrp\Sys\CoolerPhotoTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

class CoolerPhotosController extends Controller
{
    public $model;

    public function __construct(CoolerPhoto $coolerPhoto)
    {
        $this->model = $coolerPhoto;
    }

    public function index(Request $request)
    {
        $this->check();
        $cooler_ids=Cooler::whereIn('company_id',$this->company_ids)->pluck('cooler_id');
        $photos=$this->model->whereIn('cooler_id',$cooler_ids)->get();
        $fractal = new Manager();
        $sys_photos=new Collection(SysCoolerPhoto::all(),new CoolerPhotoTransformer());
        $array = $fractal->createData($sys_photos)->toArray();
        return $this->response->collection($photos,new \App\Transformers\Ccrp\CoolerPhotoTransformer())
            ->addMeta('sys_photos',$array);
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
        $photos=request()->get('photos');
        $photos=is_array($photos)?$photos:json_decode($photos,true);
        foreach ($photos as $photo)
        {
            $search=[
                'cooler_id'=>$cooler_id,
                'sys_id'=>$photo['sys_id'],
            ];
            $attribute=['value'=>$photo['value']];
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
        $photos=$this->model->where('cooler_id',$cooler->cooler_id)->get();
        $fractal = new Manager();
        $sys_photos=new Collection(SysCoolerPhoto::all(),new CoolerPhotoTransformer());
        $array = $fractal->createData($sys_photos)->toArray();
        return $this->response->collection($photos,new \App\Transformers\Ccrp\CoolerPhotoTransformer())
            ->addMeta('sys_details',$array);
    }


}
