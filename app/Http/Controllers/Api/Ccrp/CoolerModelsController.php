<?php

namespace App\Http\Controllers\Api\Ccrp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ccrp\CoolerModels;
use App\Http\Requests\Api\Ccrp\CoolerModelsRequest;
use App\Models\Ccrp\Sys\SysCoolerModel;
use App\Transformers\Ccrp\CoolerModelsTransformer;
use App\Transformers\Ccrp\Sys\CoolerModelTransformer;


class CoolerModelsController extends Controller
{
    private $cooler_models;
    private $sys_cooler_models;

    public function __construct(CoolerModels $cooler_models,SysCoolerModel $sys_cooler_models)
    {
        $this -> cooler_models = $cooler_models;
        $this -> sys_cooler_models = $sys_cooler_models;
    }

    public function index(CoolerModelsRequest $request)
    {
        $data = $request->all();
        if(isset($data['sys_brand'])){
            $sys_brand_id = DB::table('sys_cooler_brands')->where('name',$data['sys_brand'])->pluck('id');
            $populer_id = $this -> sys_cooler_models->where('brand_id',$sys_brand_id)->orderBy('popularity', 'desc')->take(10)->get(['id','name','description']);
        }else{
            $populer_id = $this -> sys_cooler_models->orderBy('popularity', 'desc')->take(10)->get(['id','name','description']);
        }
        return $this -> response ->array($populer_id, new CoolerModelTransformer());
    }

    public function show(CoolerModelsRequest $request)
    {
        $data = $request->all();
        $cooler_model = trim($data['cooler_model']);

        if(isset($data['sys_brand'])){
            $sys_brand_id = DB::table('sys_cooler_brands')->where('name', $data['sys_brand'])->pluck('id');
        }
        $str_arr = str_split($cooler_model);
        if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str_arr[0])) {
            if (!isset($sys_brand_id)) {
                $model_res = $this->sys_cooler_models->selectRaw("concat(description,'(',name,')') as modelname,name as sys_model,LOCATE('".$cooler_model."',description) as num,brand_id")
                    ->where('description','like','%'.$cooler_model.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            } else {
                $model_res = $this->sys_cooler_models->selectRaw("concat(description,'(',name,')') as modelname,name as sys_model,LOCATE('".$cooler_model."',description) as num,brand_id")
                    ->where([['description','like','%'.$cooler_model.'%'], ['brand_id','=',$sys_brand_id]])->orderBy('num')->orderBy('popularity','desc')->get();
            }
        }else{
            if (!isset($sys_brand_id)) {
                $model_res = $this->sys_cooler_models->selectRaw("concat(name,'(',description,')') as modelname,name as sys_model,LOCATE('".$cooler_model."',name) as num,brand_id")
                    ->where('name','like','%'.$cooler_model.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            } else {
                $model_res = $this->sys_cooler_models->selectRaw("concat(name,'(',description,')') as modelname,name as sys_model,LOCATE('".$cooler_model."',name) as num,brand_id")
                    ->where([['name','like','%'.$cooler_model.'%'],['brand_id','=',$sys_brand_id]])->orderBy('num')->orderBy('popularity','desc')->get();
            }
        }
        if (count($model_res)>0) {
            return $this->response->item($model_res, new CoolerModelTransformer());
        }else{
            if(isset($sys_brand_id)){
                $user_brand_res = $this->cooler_models->selectRaw("user_model as modelname,sys_model,LOCATE('".$cooler_model."',user_model) as num")->where([['user_model','like','%'.$cooler_model.'%'],['sys_brand_id','=',$sys_brand_id]])->orderBy('num')->orderBy('popularity','desc')->get();
            }else{
                $user_brand_res = $this->cooler_models->selectRaw("user_model as modelname,sys_model,LOCATE('".$cooler_model."',user_model) as num")->where('user_model','like','%'.$cooler_model.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            }
            if(count($user_brand_res)>0){
                return $this->response->item($model_res, new CoolerModelsTransformer());
            }else{
                return $this->response -> noContent();
            }

        }
    }
    public function store(CoolerModelsRequest $request)
    {
        $model = $request -> all();
        $user_model = trim($model['cooler_model']);
        if(isset($model['sys_brand'])){
            $sys_brand_id = DB::table('sys_cooler_brands')->where('name', $model['sys_brand'])->pluck('id');
        }
        if(isset($model['sys_model'])){
            if(isset($sys_brand_id)){
                $this->cooler_models->where([['sys_model',$model['sys_model']],['user_model',$user_model],['sys_brand_id',$sys_brand_id[0]]])->increment('popularity');
                $data = $this->cooler_models->where([['sys_model',$model['sys_model']],['user_model',$user_model],['sys_brand_id',$sys_brand_id[0]]])->first();
            }else{
                $this->cooler_models->where([['sys_model',$model['sys_model']],['user_model',$user_model]])->increment('popularity');
                $data = $this->cooler_models->where([['sys_model',$model['sys_model']],['user_model',$user_model]])->first();
            }
        }else{
            if(isset($sys_brand_id)){
                $new['sys_brand_id'] = $sys_brand_id[0];
            }
            $new['user_model'] = $user_model;
            $data = $this->cooler_models->create($new);
        }
        return $this->response->item($data, new CoolerModelsTransformer())->setStatusCode(201);
    }
}
