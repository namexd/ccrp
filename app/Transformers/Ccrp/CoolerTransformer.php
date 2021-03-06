<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\VaccineTags;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['collectors', 'statCooler', 'category', 'company','vaccine_tags','details','photos','logs'];

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
            return $this->null();
    }

    public function includeVaccineTags(Cooler $cooler)
    {

        $vaccine_tags=$cooler->vaccine_tags;
        return $this->collection($vaccine_tags,new VaccineTagTransformer());
    }
    public function includeDetails(Cooler $cooler)
    {
        return $this->collection($cooler->details()->whereRaw(' (locate('.$cooler->cooler_type.',note) or length(note)=0 or ISNULL(note))')->get()->toArray(),function ($arr){
            return $arr;
        });
    }

    public function includePhotos(Cooler $cooler)
    {
        return $this->collection($cooler->photos()->whereRaw(' (locate('.$cooler->cooler_type.',note) or length(note)=0 or ISNULL(note))')->get()->toArray(),function ($arr){
            $arr['pivot']['value']=$arr['pivot']['value']?config('app.we_url').'/files/'.$arr['pivot']['value'] : '';;
            return $arr;
        });

    }
    public function includeLogs(Cooler $cooler)
    {
        if ($cooler->logs()->get()->isNotEmpty())
        return $this->item($cooler->logs->last()->toArray(),function ($array){
            return array_only($array,'note');
        });
        else
            return $this->null();
    }
}