<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\CompanyDetail;
use League\Fractal\TransformerAbstract;

class CompanyDetailTableTransformer extends TransformerAbstract
{
    public $availableIncludes=['sys_detail'];
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

    public function includeSysDetail(CompanyDetail $companyDetail)
    {
        return $this->item($companyDetail->sys_detail,new \App\Transformers\Ccrp\Sys\CompanyDetailTransformer());
    }
}