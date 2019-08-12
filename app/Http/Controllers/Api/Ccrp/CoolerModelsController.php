<?php

namespace App\Http\Controllers\Api\Ccrp;

use Illuminate\Http\Request;
use App\Models\Ccrp\CoolerModels;
use App\Http\Requests\Api\Ccrp\CoolerModelsRequest;
use App\Models\Ccrp\Sys\SysCoolerModel;
use App\Models\Ccrp\Sys\SysCoolerBrand;
use App\Transformers\Ccrp\CoolerModelsTransformer;
use App\Transformers\Ccrp\Sys\CoolerModelTransformer;


class CoolerModelsController extends Controller
{
    private $cooler_models;
    private $sys_cooler_models;

    public function __construct(CoolerModels $cooler_models,SysCoolerModel $sys_cooler_models,SysCoolerBrand $sys_cooler_brands)
    {
        $this -> cooler_models = $cooler_models;
        $this -> sys_cooler_models = $sys_cooler_models;
        $this -> sys_cooler_brands = $sys_cooler_brands;
    }

    public function index(Request $request)
    {
        $pagesize = $request->get('pagesize')??'10';
        $data = $request->all();
        if(isset($data['sys_brand'])) {
            $sys_brand_id = $this->sys_cooler_brands->where('name', $data['sys_brand'])->pluck('id');
        }
        if(!isset($sys_brand_id[0])) {
            $populer_id = $this -> sys_cooler_models->orderBy('popularity', 'desc')->take($pagesize)->get();
        }else{

            $populer_id = $this -> sys_cooler_models->where('brand_id',$sys_brand_id)->orderBy('popularity', 'desc')->take($pagesize)->get();
        }
        return $this -> response ->collection($populer_id, new CoolerModelTransformer());
    }

    public function show(CoolerModelsRequest $request)
    {
        $data = $request->all();
        $cooler_model = trim($data['cooler_model']);
        if(isset($data['sys_brand'])){
            $sys_brand_id =  $this -> sys_cooler_brands->where('name', $data['sys_brand'])->pluck('id');
        }
        $str_arr = str_split($cooler_model);
        if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str_arr[0])) {
            if (!isset($sys_brand_id[0])) {
                $model_res = $this->sys_cooler_models->selectRaw("*,LOCATE('".$cooler_model."',comment) as num")
                    ->where('comment','like','%'.$cooler_model.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            } else {
                $model_res = $this->sys_cooler_models->selectRaw("*,LOCATE('".$cooler_model."',description) as num")
                    ->where([['comment','like','%'.$cooler_model.'%'], ['brand_id','=',$sys_brand_id]])->orderBy('num')->orderBy('popularity','desc')->get();
            }
        }else{
            if (!isset($sys_brand_id[0])) {
                $model_res = $this->sys_cooler_models->selectRaw("*,LOCATE('".$cooler_model."',name) as num")
                    ->where('name','like','%'.$cooler_model.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            } else {
                $model_res = $this->sys_cooler_models->selectRaw("*,LOCATE('".$cooler_model."',name) as num")
                    ->where([['name','like','%'.$cooler_model.'%'],['brand_id','=',$sys_brand_id]])->orderBy('num')->orderBy('popularity','desc')->get();
            }
        }
        if (count($model_res)>0) {
            return $this->response->collection($model_res, new CoolerModelTransformer());
        }else{
            if(isset($sys_brand_id[0])){
                $user_brand_res = $this->cooler_models->selectRaw("*,LOCATE('".$cooler_model."',user_model) as num")->where([['user_model','like','%'.$cooler_model.'%'],['sys_brand_id','=',$sys_brand_id]])->orderBy('num')->orderBy('popularity','desc')->get();
            }else{
                $user_brand_res = $this->cooler_models->selectRaw("*,LOCATE('".$cooler_model."',user_model) as num")->where('user_model','like','%'.$cooler_model.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            }
            if(count($user_brand_res)>0){
                return $this->response->collection($user_brand_res, new CoolerModelsTransformer());
            }else{
                return $this->response -> noContent();
            }

        }
    }
}
