<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\SysCompanyDetail;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyDetailTransformer extends TransformerAbstract
{
    public function transform(SysCompanyDetail $companyDetail)
    {
        $arr=[
            'id'=>$companyDetail->id,
            'name'=>$companyDetail->name,
            'category'=>$companyDetail->category,
            'slug'=>$companyDetail->slug,
            'value'=>$companyDetail->value,
            'description'=>$companyDetail->description,
            'note'=>$companyDetail->note,
            'created_at'=>Carbon::parse($companyDetail->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($companyDetail->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}