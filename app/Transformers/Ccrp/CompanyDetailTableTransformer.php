<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\CompanyDetail;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyDetailTableTransformer extends TransformerAbstract
{
    public $availableIncludes=['cooler'];
    public function transform(CompanyDetail $companyDetail)
    {
        $arr=[
            'id' => $companyDetail->id,
            'company_id' => $companyDetail->company_id,
            'sys_id' => $companyDetail->sys_id,
            'value' => $companyDetail->value,
            'created_at' => $companyDetail->created_at->toDateTimeString(),
            'updated_at' => $companyDetail->updated_at->toDateTimeString()
        ];
        return $arr;
    }
}