<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
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
        if ($company->warning_sender_events)
        {
            $arr['warning_sender_events'] =$company->warning_sender_events;
        }
        if ($company->warning_events)
        {
            $arr['warning_events'] =$company->warning_events;
        }
        if ($company->coolers)
        {
            $arr['coolers'] =$company->coolers;
        }
        return $arr;
    }
}