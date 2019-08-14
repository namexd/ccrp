<?php

namespace App\Admin\Controllers\Ccrp;

use App\Http\Controllers\Controller;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Reports\StatMange;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Input;

class ReportsController extends Controller
{
    /**
     * note：冷链管理评估表
     * @return Response
     */
    public function statManage()
    {
        $company_id=Input::get('company_id');
        $company=Company::find($company_id);
        $result=(new StatMange())->getListByMonths($company->ids(),Input::get('start'),Input::get('end'));
        return response()->json($result);
    }

}
