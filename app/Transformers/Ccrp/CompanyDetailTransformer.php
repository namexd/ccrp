<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyDetailTransformer extends TransformerAbstract
{
    public $availableIncludes=['cooler'];
    public function transform(Company $company)
    {
        $arr=[
            'id' => $company->id,
            'pid' => $company->pid,
            'title' => $company->title,
            'short' => $company->short_title,
            'office_title' => $company->office_title,
            'nipis_code' => $company->nipis_code,
            'cdc_code' => $company->cdc_code,
            'custome_code' => $company->custome_code,
            'company_group' => $company->company_group,
            'company_type' =>Company::COMPANY_TYPE[$company->company_type],
            'address' => $company->address,
            'address_lat' => $company->address_lat,
            'address_lon' => $company->address_lon,
            'manager' => $company->manager,
            'tel' => $company->tel,
            'phone' => $company->phone,
            'email' => $company->email,
            'username' => $company->username,
            'password' => $company->password,
            'poweroff_send_type' => $company->poweroff_send_type,
            'offline_send_type' => $company->offline_send_type,
            'cdc_warningeredit_menu' => $company->cdc_warningeredit_menu,
            'category_count_has_cooler' => $company->category_count_has_cooler,
            'shebei_install' => $company->shebei_install,
            'shebei_actived' => $company->shebei_actived,
            'area_level1_id' => $company->area_level1_id,
            'area_level2_id' => $company->area_level2_id,
            'area_level3_id' => $company->area_level3_id,
            'area_level4_id' => $company->area_level4_id,
        ];
        return $arr;
    }
}