<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\User;
use App\Http\Controllers\Api\Controller as BaseController;
use App\Models\Microservice\MicroserviceAPI;

class Controller extends BaseController
{
    public $app_id = 1;
    public $user;
    public $company;
    public $company_ids;
    public $userinfo;

    public function user()
    {
        return $this->userinfo;
    }
    public function check($company_id = null)
    {

        $access = session()->get('access');
        if($access and $access['info'])
        {
            $info = $access['info'];
            $this->userinfo = $info['userinfo'];
            $user = User::where('id', $info['userid'])->first();
        }else{
            return $this->response->error('系统账号绑定错误', 457);
        }
        if ($user->status == 0) {
            return $this->response->error('系统账号验证错误', 457);
        } else {

            if ($company_id == null) {
                if (request()->get('company_id')) {
                    $company_id = request()->get('company_id');
                    $user_company = $user->userCompany;
                    $user_company_ids = $user_company->ids();
                    if (!in_array($company_id, $user_company_ids)) {
                        $company_id = $user->company_id;
                    }
                }else{
                    $company_id = $user->company_id;
                }

            } else {
                $user_company = $user->userCompany;
                if (!$user_company) {
                    return $this->response->error('系统账号验证错误', 457);
                }
                $user_company_ids = $user_company->ids();
                if (!in_array($company_id, $user_company_ids)) {
                    $company_id = $user->company_id;
                }
            }


            $this->user = $user;
            $this->company = Company::where('id', $company_id)->first();

            $ids = $this->company ? $this->company->ids() : [];
            $this->company_ids = $ids;
            \Auth::loginUsingId($info['userinfo']['id']);
        }
    }


}
