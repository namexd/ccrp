<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public $app_id = 1;
    public $user;
    public $company;
    public $company_ids;
    public $response;

    public function __construct()
    {
        $this->response = response();
    }

    public function lwResponse($data, $transfer, $addMeta = null, $resourceKey = false)
    {
        $datas = fractal($data, $transfer)->toArray();
        $return = null;
        $return = ($resourceKey) ? [$resourceKey => $datas] : $datas;

        if ($addMeta) {
            $return['meta'] = $addMeta;
        }
        return $this->response->json($return);
    }

    public function user()
    {
        return User::first();
    }


    public function check($company_id = null)
    {
        $user = $this->user();

        if ($user->status == 0) {
            return $this->response->error('系统账号验证错误', 457);
        } else {

            if ($company_id == null) {
                $company_id = $user->company_id;
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
        }
    }
}
