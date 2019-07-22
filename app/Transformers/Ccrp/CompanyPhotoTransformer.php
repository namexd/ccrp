<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\CompanyDetail;
use App\Models\Ccrp\CompanyPhoto;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyPhotoTransformer extends TransformerAbstract
{
    public $availableIncludes=['cooler'];
    public function transform(CompanyPhoto $companyPhoto)
    {
        $arr=[
            'id' => $companyPhoto->id,
            'company_id' => $companyPhoto->company_id,
            'sys_id' => $companyPhoto->sys_id,
            'value' => $companyPhoto->value ? config('app.we_url').'/files/'.$companyPhoto->value : '',
            'created_at' => $companyPhoto->created_at->toDateTimeString(),
            'updated_at' => $companyPhoto->updated_at->toDateTimeString()
        ];
        return $arr;
    }
}