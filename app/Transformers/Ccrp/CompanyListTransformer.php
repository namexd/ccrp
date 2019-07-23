<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyListTransformer extends TransformerAbstract
{
    public $availableIncludes = ['details', 'photos'];

    public function transform(Company $company)
    {
        $arr = [
            'id' => $company->id,
            'pid' => $company->pid,
            'title' => $company->title,
            'short' => $company->short_title,
            'address' => $company->address,
            'address_lat' => $company->address_lat,
            'address_lon' => $company->address_lon,
            'category' => $company->categories,
        ];
        return $arr;
    }

    public function includeDetails(Company $company)
    {
        return $this->collection($company->details->toArray(),function ($arr){
            return $arr;
        });
    }

    public function includePhotos(Company $company)
    {
        return $this->collection($company->photos->toArray(),function ($arr){
            $arr['pivot']['value']=$arr['pivot']['value']?config('app.we_url').'/files/'.$arr['pivot']['value'] : '';;
            return $arr;
        });

    }
}