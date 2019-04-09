<?php

namespace App\Transformers;

use App\Models\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyListTransformer extends TransformerAbstract
{
    public $availableIncludes=['cooler'];
    public function transform(Company $company)
    {
        $arr=[
            'id' => $company->id,
            'pid' => $company->pid,
            'title' => $company->title,
            'short' => $company->short_title,
            'address' => $company->address,
            'address_lat' => $company->address_lat,
            'address_lon' => $company->address_lon
        ];
        return $arr;
    }
}
