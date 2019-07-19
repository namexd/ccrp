<?php

namespace App\Http\Controllers\Api\Ccrp\Sys;

use App\Http\Controllers\Api\Ccrp\Controller;
use App\Http\Requests\Api\Ccrp\CompanyRequest;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sys\CompanyDetail;


class CompanyDetailsController extends Controller
{
    public $model;

    public function __construct(CompanyDetail $companyDetail)
    {
        $this->model = $companyDetail;
    }

    public function index(CompanyRequest $request)
    {
        $this->check();

    }

}
