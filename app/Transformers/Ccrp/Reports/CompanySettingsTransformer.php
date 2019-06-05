<?php

namespace App\Transformers\Ccrp\Reports;

use App\Models\Ccrp\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanySettingsTransformer extends TransformerAbstract
{
    public function transform(Company $company)
    {
        $info= [
            'pid' => $company->pid,
            'title' => $company->title,
            'short' => $company->short_title,
            'manager' => $company->manager,
            'address' => $company->address,
            'region_name' => $company->region_code?$company->region_name:'',
            'address_lat' => $company->address_lat,
            'address_lon' => $company->address_lon,
            "shebei_install" => $company->shebei_install,
            "shebei_actived" => $company->shebei_actived,

        ];
        return $info;
    }
}