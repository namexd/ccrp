<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CoolerTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors', 'statCooler', 'category', 'company','vaccine_tags','details','photos'];

    public function transform(Cooler $cooler)
    {
        $arr = [
            'id' => $cooler->cooler_id,
            'cooler_sn' => $cooler->cooler_sn,
            'cooler_name' => $cooler->cooler_name,
            'brand' => $cooler->cooler_brand,
            'model' => $cooler->cooler_model,
            'size' => $cooler->cooler_size,
            'size2' => $cooler->cooler_size2,
            'is_medical' =>$cooler->is_medical,
            'is_medical_name' =>is_null($cooler->is_medical)?'未知':Cooler::IS_MEDICAL[$cooler->is_medical],
            'cooler_type' => $cooler->cooler_type != 0 ? Cooler::COOLER_TYPE[$cooler->cooler_type] : '未知',
            'company_id' => $cooler->company_id,
            'status' => $cooler->status != 0 ? Cooler::$status[$cooler->status] : '未知',
            'company' => $cooler->company->title ?? '',
            'created_at' => $cooler->install_time > 0 ? Carbon::createFromTimestamp($cooler->install_time)->toDateTimeString() : 0,
            'updated_at' => $cooler->update_time > 0 ? Carbon::createFromTimestamp($cooler->update_time)->toDateTimeString() : 0,
        ];
        if ($cooler->url) {
            $arr['url'] = $cooler->url;
        }
        $arr['image'] = $cooler->cooler_image ?? '';
        return $arr;
    }

    public function includeCompany(Cooler $cooler)
    {
        return $this->item($cooler->company, new CompanyListTransformer());
    }

    public function includeCollectors(Cooler $cooler)
    {
        return $this->collection($cooler->collectorsOnline, new CollectorIncludeTransformer());
    }

    public function includeStatCooler(Cooler $cooler)
    {
        $date = request()->get('date') ?? date('Y-m', strtotime('-1 Month'));
        return $this->collection($cooler->statCooler->where('month', $date), new StatCoolerTransformer());
    }

    public function includeCategory(Cooler $cooler)
    {
        if ($cooler->cooler_category)
        return $this->item($cooler->cooler_category, new CoolerCategoryTransformer());
        else
            return new Item(null,function (){
               return [];
            });
    }

    public function includeVaccineTags(Cooler $cooler)
    {
        return $this->collection($cooler->vaccine_tags,new VaccineTagTransformer());
    }
    public function includeDetails(Cooler $cooler)
    {
        return $this->collection($cooler->details->toArray(),function ($arr){
            return $arr;
        });
    }

    public function includePhotos(Cooler $cooler)
    {
        return $this->collection($cooler->photos->toArray(),function ($arr){
            return $arr;
        });

    }
}