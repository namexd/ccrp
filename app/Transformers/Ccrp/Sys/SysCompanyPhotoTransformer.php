<?php

namespace App\Transformers\Ccrp\Sys;

use App\Models\Ccrp\Sys\SysCompanyDetail;
use App\Models\Ccrp\Sys\SysCompanyPhoto;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SysCompanyPhotoTransformer extends TransformerAbstract
{
    public function transform(SysCompanyPhoto $companyPhoto)
    {
        $arr=[
            'id'=>$companyPhoto->id,
            'name'=>$companyPhoto->name,
            'category'=>$companyPhoto->category,
            'slug'=>$companyPhoto->slug,
            'value'=>$companyPhoto->value,
            'description'=>$companyPhoto->description,
            'note'=>$companyPhoto->note,
            'created_at'=>Carbon::parse($companyPhoto->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($companyPhoto->updated_at)->toDateTimeString(),
        ];
        return $arr;
    }
}