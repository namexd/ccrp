<?php

namespace App\Http\Controllers\Api\Ccrp;
use Illuminate\Http\Request;
use App\Models\Ccrp\CoolerBrands;
use App\Http\Requests\Api\Ccrp\CoolerBrandsRequest;
use App\Models\Ccrp\Sys\SysCoolerBrand;
use App\Transformers\Ccrp\CoolerBrandsTransformer;
use App\Transformers\Ccrp\Sys\CoolerBrandTransformer;

class CoolerBrandsController extends Controller
{
    private $cooler_brands;
    private $sys_cooler_brands;

    public function __construct(CoolerBrands $cooler_brands,SysCoolerBrand $sys_cooler_brands)
    {
        $this -> cooler_brands = $cooler_brands;
        $this -> sys_cooler_brands = $sys_cooler_brands;
    }
    public function index(Request $request)
    {
        $pagesize = $request->get('pagesize')??'10';
        $popular_brands = $this->sys_cooler_brands->orderBy('popularity', 'desc')->take($pagesize)->get(['id','name','slug']);
        return $this->response->collection($popular_brands, new CoolerBrandTransformer());
    }

    public function show(CoolerBrandsRequest $request)
    {
        $data = $request -> all();
        $cooler_brand = trim($data['cooler_brand']);
        $str_arr = str_split($cooler_brand);
        if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str_arr[0])){
            $brand_res = $this->sys_cooler_brands->selectRaw("name as brandname,name as sys_brand,LOCATE('".$cooler_brand."',name) as num")
                ->where('name', 'like', '%'.$cooler_brand.'%')->orderBy('num')->orderBy('popularity', 'desc')->get();
        }else{
            $brand_res = $this->sys_cooler_brands->selectRaw("slug as brandname,name as sys_brand,LOCATE('".$cooler_brand."',slug) as num")
                ->where('slug', 'like', '%'.$cooler_brand.'%')->orderBy('num')->orderBy('popularity','desc')->get();
        }
        if(count($brand_res)>0){
            return $this->response->item($brand_res, new CoolerBrandTransformer());
        }else{
            $user_brand_res = $this->cooler_brands->selectRaw("user_brand as brandname,sys_brand,LOCATE('".$cooler_brand."',user_brand) as num")->where('user_brand','like','%'.$cooler_brand.'%')->orderBy('num')->orderBy('popularity','desc')->get();
            if (count($user_brand_res)>0) {
                return $this->response->item($user_brand_res, new CoolerBrandsTransformer());
            } else {
                return $this->response -> noContent();
            }
        }
    }
    public function store(CoolerBrandsRequest $request)
    {
        $brand = $request -> all();
        $user_brand = trim($brand['cooler_brand']);
        if(isset($brand['sys_brand'])){
            $sys_brand_id = $this->sys_cooler_brands->where([['name',$brand['sys_brand']],['name',$user_brand]])->orWhere([['name',$brand['sys_brand']],['slug',$user_brand]])->pluck('id');
            if(isset($sys_brand_id[0])){
                $this->sys_cooler_brands->where('id',$sys_brand_id)->increment('popularity',1);
                $data = $this->sys_cooler_brands->where('id',$sys_brand_id)->get();
                return $this->response->item($data, new CoolerBrandTransformer())->setStatusCode(201);
            }else{
                $this->cooler_brands->where([['sys_brand',$brand['sys_brand']],['user_brand',$user_brand]])->increment('popularity',1);
                $data = $this->cooler_brands->where([['sys_brand',$brand['sys_brand']],['user_brand',$user_brand]])->first();
            }
        }else{
            $new['user_brand'] = $user_brand;
            $data = $this->cooler_brands->create($new);
        }
        return $this->response->item($data, new CoolerBrandsTransformer())->setStatusCode(201);
    }
}
