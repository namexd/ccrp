<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyInfoTransformer extends TransformerAbstract
{
    public function transform(Company $company)
    {
        $info= [
            'id' => $company->id,
            'pid' => $company->pid,
            'title' => $company->title,
            'short' => $company->short_title,
            'address' => $company->address,
            'address_lat' => $company->address_lat,
            'address_lon' => $company->address_lon,
            "category_count" => $company->category_count,
            "category_count_has_cooler" => $company->category_count_has_cooler,
            "cdc_admin" => $company->cdc_admin,
            "shebei_install" => $company->shebei_install,
            "shebei_install_type1" => $company->shebei_install_type1,
            "shebei_install_type2" => $company->shebei_install_type2,
            "shebei_install_type3" => $company->shebei_install_type3,
            "shebei_install_type4" => $company->shebei_install_type4,
            "shebei_install_type5" => $company->shebei_install_type5,
            "shebei_install_type6" => $company->shebei_install_type6,
            "shebei_install_type7" => $company->shebei_install_type7,
            "shebei_install_type8" => $company->shebei_install_type8,
            "shebei_install_type9" => $company->shebei_install_type9,
            "shebei_install_type10" => $company->shebei_install_type10,
            "shebei_install_type11" => $company->shebei_install_type11,
            "shebei_install_type12" => $company->shebei_install_type12,
            "shebei_install_type100" => $company->shebei_install_type100,
            "shebei_install_type101" => $company->shebei_install_type101,
            "shebei_actived" => $company->shebei_actived,
            "shebei_actived_type1" => $company->shebei_actived_type1,
            "shebei_actived_type2" => $company->shebei_actived_type2,
            "shebei_actived_type3" => $company->shebei_actived_type3,
            "shebei_actived_type4" => $company->shebei_actived_type4,
            "shebei_actived_type5" => $company->shebei_actived_type5,
            "shebei_actived_type6" => $company->shebei_actived_type6,
            "shebei_actived_type7" => $company->shebei_actived_type7,
            "shebei_actived_type8" => $company->shebei_actived_type8,
            "shebei_actived_type9" => $company->shebei_actived_type9,
            "shebei_actived_type10" => $company->shebei_actived_type10,
            "shebei_actived_type11" => $company->shebei_actived_type11,
            "shebei_actived_type12" => $company->shebei_actived_type12,
            "shebei_actived_type100" => $company->shebei_actived_type100,
            "shebei_actived_type101" => $company->shebei_actived_type101,
            "alerms_all" => $company->alerms_all,
            "alerms_today" => $company->alerms_today,
            "alerms_new" => $company->alerms_new,
        ];

        if($company->users[0] and $avatar = $company->users[0]->avatarImage)
        {
            $info['image'] = $avatar->path;
        }else{
            if($company->cdcLevel()>0)
            {
                $info['image'] = config('api.defaults.image.logo.cdc');
            }else{
                $info['image'] = config('api.defaults.image.logo.default');
            }
        }
        return $info;
    }
}