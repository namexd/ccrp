<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Requests\Api\Ccrp\Report\DateRangeRequest;
use App\Http\Requests\Api\Ccrp\Report\MonthRequest;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Reports\CoolerLog;
use App\Models\Ccrp\Sys\SysCoolerBrand;
use App\Models\Ccrp\Sys\SysCoolerModel;
use App\Transformers\Ccrp\Reports\CoolerLogTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Input;


/**
 * 冷链操作日志表
 * Class CoolersController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class CoolersController extends Controller
{
    /**
     * note：冷链操作日志表
     * author: xiaodi
     * date: 2019/3/26 15:43
     * @param DateRangeRequest $request
     * @param CoolerLog $coolerLog
     * @return Response
     */
    public function logs(DateRangeRequest $request, CoolerLog $coolerLog)
    {
        $this->check($this->company_id);
        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        $result = $coolerLog->getListByDate($this->company_ids, $start, $end)->paginate(request()->get('pagesize') ?? $this->pagesize);
        $transformer = new CoolerLogTransformer();
        return $this->response->paginator($result, $transformer)->addMeta('colums', $transformer->columns());
    }

    public function countCoolerNumber(Cooler $cooler)
    {
        $this->check($this->company_id);
        $company_id = $this->company_id ?? $this->company->id;
        $parent = Company::find($company_id);
        $companies = Company::where('pid', $company_id)->where('status', 1)->where('company_group', $parent->company_group)->get();
        if ($companies->isEmpty())
        {
            $companies=Company::where('id',$company_id)->get();
        }
        $result = [];
        foreach ($companies as $key => $company) {
            $companyIds = $company->ids();
            $result[$key] = $cooler->getCountByType($companyIds, request()->toArray());
            $result[$key]['title'] = $company->title;
            $result[$key]['region_code'] = $company->region_code;
        }
        $key_arrays=array_first($result);
        array_forget($key_arrays,['title','region_code']);
        $key_arrays= array_keys($key_arrays);
        $count_array=[];
        foreach ($key_arrays as $array)
        {
            $count_array[$array]= array_sum(array_column($result,$array));
        }
        $count_array['title']='合计';
        $count_array['region_code']='';
        array_push($result,$count_array);
        $data['data'] = $result;
        $data['meta']['columns']['parent'] = $parent->title;
        return $this->response->array($data);

    }

    public function countCoolerVolume(Cooler $cooler)
    {
        $this->check($this->company_id);
        $company_id = $this->company_id ?? $this->company->id;
        $parent = Company::find($company_id);
        $companies = Company::where('pid', $company_id)->where('status', 1)->where('company_group', $parent->company_group)->get();
        if ($companies->isEmpty())
        {
            $companies=Company::where('id',$company_id)->get();
        }
        $result = [];
        foreach ($companies as $key => $company) {
            $companyIds = $company->ids();
            $result[$key] = $cooler->getVolumeByStatus($companyIds, request()->toArray());
            $result[$key]['title'] = $company->title;
            $result[$key]['region_code'] = $company->region_code;
        }
        $key_arrays=array_first($result);
        array_forget($key_arrays,['title','region_code']);
        $key_arrays= array_keys($key_arrays);
        $count_array=[];
        foreach ($key_arrays as $array)
        {
            $count_array[$array]= array_sum(array_column($result,$array));
        }
        $count_array['title']='合计';
        $count_array['region_code']='';
        array_push($result,$count_array);
        $data['data'] = $result;
        $data['meta']['columns']['parent'] = $parent->title;
        return $this->response->array($data);

    }

    public function countCoolerStatus(Cooler $cooler)
    {
        $this->check($this->company_id);
        $company_id = $this->company_id ?? $this->company->id;
        $parent = Company::find($company_id);
        $companies = Company::where('pid', $company_id)->where('status', 1)->where('company_group', $parent->company_group)->get();
        if ($companies->isEmpty())
        {
            $companies=Company::where('id',$company_id)->get();
        }
        $result = [];
        foreach ($companies as $key => $company) {
            $companyIds = $company->ids();
            $result[$key] = $cooler->getCoolerStatus($companyIds, request()->toArray());
            $result[$key]['title'] = $company->title;
            $result[$key]['region_code'] = $company->region_code;
        }
        $key_arrays=array_first($result);
        array_forget($key_arrays,['title','region_code']);
        $key_arrays= array_keys($key_arrays);
        $count_array=[];
        foreach ($key_arrays as $array)
        {
            $count_array[$array]= array_sum(array_column($result,$array));
        }
        $count_array['title']='合计';
        $count_array['region_code']='';
        array_push($result,$count_array);
        $data['data'] = $result;
        $data['meta']['columns']['parent'] = $parent->title;
        return $this->response->array($data);

    }

    public function getCoolerTypes(Cooler $cooler)
    {
        $result = $cooler->getCoolerTypes();
        return $this->response->array($result);
    }

    public function sysBrands()
    {
        $sysBrands = SysCoolerBrand::orderBy('popularity', 'desc')->get();
        foreach ($sysBrands as $item) {
            $info['data'][] = [
                "title" => $item->comporation,
                'meta' => [
                    "header" => $item->name .' (热度:'.$item->popularity.')',
                    "detail_data" => '/api/ccrp/reports/coolers/sys/models/' . $item->id . '?with=columns',
                    "detail_template" => 'list'
                ]
            ];
        }

        $info["meta"]["columns"] = [
            [
                "label" => "",
                "value" => "title"
            ]
        ];
        return $this->response->array($info);
    }

    public function sysModels($brand)
    {
        $sysBrand = SysCoolerBrand::where('id', $brand)->first();
        $sysModels = SysCoolerModel::where('brand_id', $brand)->orderBy('popularity','desc')->get();
        foreach ($sysModels as $item) {
            if ($item->type) {
                $type = $item->type;
                $typename = $type->name;
            } else {
                $typename = '';
            }
            $info['data'][] = [
                "title" => $sysBrand->name. ' ' . $item->name . ' ' . $typename .  ($item->is_medical==1?' [医用]':''),
                'meta' => [
                    "header" => $item->name .' (热度:'.$item->popularity.')',
                    "detail_data" => '/api/ccrp/reports/coolers/sys/models_details/' . $item->id . '?with=columns',
                    "detail_template" => 'detail'
                ]
            ];
        }

        $info["meta"]["columns"] = [
            [
                "label" => "",
                "value" => "title"
            ]
        ];
        return $this->response->array($info);
    }

    public function sysModelsDetail($model)
    {
        $item = SysCoolerModel::where('id', $model)->first();

        $info["brand"] = $item->brand->name;
        $info["meta"]["columns"][] =
            [
                "label" => "品牌",
                "value" => "brand"
            ];
        $info["model"] = $item->name;
        $info["meta"]["columns"][] =
            [
                "label" => "型号",
                "value" => "model"
            ];

        if ($item->type) {
            $info["type"] = $item->type->name;
            $info["meta"]["columns"][] =
                [
                    "label" => "类型",
                    "value" => "type"
                ];
        }
        if ($item->is_medical) {
            $info["is_medical"] = $item->is_medical==1?'医用':'';
            $info["meta"]["columns"][] =
                [
                    "label" => "是否医用",
                    "value" => "is_medical"
                ];
        }
        if ($item->warmarea_count) {
            $info["warmarea_count"] = $item->warmarea_count;
            $info["meta"]["columns"][] =
                [
                    "label" => "温区",
                    "value" => "warmarea_count"
                ];
        }
        if ($item->body_type) {
            $info["body_type"] = $item->body_type;
            $info["meta"]["columns"][] =
                [
                    "label" => "柜体",
                    "value" => "body_type"
                ];
        }
        if ($item->cool_volume) {
            $info["cool_volume"] = $item->cool_volume;
            $info["meta"]["columns"][] =
                [
                    "label" => "冷藏容积(L)",
                    "value" => "cool_volume"
                ];
        }
        if ($item->cold_volume) {
            $info["cold_volume"] = $item->cold_volume;
            $info["meta"]["columns"][] =
                [
                    "label" => "冷冻容积(L)",
                    "value" => "cold_volume"
                ];
        }
        if ($item->whole_volume) {
            $info["whole_volume"] = $item->whole_volume;
            $info["meta"]["columns"][] =
                [
                    "label" => "总容积(L)",
                    "value" => "whole_volume"
                ];
        }
        if ($item->weight) {
            $info["weight"] = $item->weight;
            $info["meta"]["columns"][] =
                [
                    "label" => "重量",
                    "value" => "weight"
                ];
        }
        if ($item->power) {
            $info["power"] = $item->power;
            $info["meta"]["columns"][] =
                [
                    "label" => "用电",
                    "value" => "power"
                ];
        }
        if ($item->specifications) {
            $info["specifications"] = $item->specifications;
            $info["meta"]["columns"][] =
                [
                    "label" => "体积",
                    "value" => "specifications"
                ];
        }
        if ($item->temperature) {
            $info["temperature"] = $item->temperature;
            $info["meta"]["columns"][] =
                [
                    "label" => "温度",
                    "value" => "temperature"
                ];
        }
        if ($item->comment) {
            $info["comment"] = $item->comment;
            $info["meta"]["columns"][] =
                [
                    "label" => "说明",
                    "value" => "comment"
                ];
        }
        if ($item->medical_licence) {
            $info["medical_licence"] = $item->medical_licence;
            $info["meta"]["columns"][] =
                [
                    "label" => "注册证编号",
                    "value" => "medical_licence"
                ];
        }
        if ($item->description) {
            $info["description"] = $item->description;
            $info["meta"]["columns"][] =
                [
                    "label" => "描述",
                    "value" => "description"
                ];
        }
        if ($item->picture) {
            $info["picture"] = $item->picture;
            $info["meta"]["columns"][] =
                [
                    "label" => "图片",
                    "value" => "picture"
                ];
        }

        return $this->response->array($info);
    }
}
