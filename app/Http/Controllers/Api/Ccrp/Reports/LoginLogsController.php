<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Requests\Api\Ccrp\Report\DateRangeRequest;
use App\Http\Requests\Api\Ccrp\Report\MonthRequest;
use App\Models\Ccrp\Reports\LoginLog;
use App\Transformers\Ccrp\Reports\LoginLogTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Illuminate\Support\Facades\Input;


/**
 * 登录日志统计表
 * Class LoginLogsController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class LoginLogsController extends Controller
{
    /**
     * note：登陆日志统计表
     * author: xiaodi
     * date: 2019/4/01 13:43
     * @param MonthRequest $request
     * @param LoginLog $loginLog
     * @return
     */
    public function statistics(MonthRequest $request,LoginLog $loginLog)
    {
        $this->check($this->company_id);
        $date =Input::get('month');
        $login_logs['data']= $loginLog->getReportByMonth($this->company_ids,$date);
        return $this->response->array($login_logs);
    }

    /**
     * note：登陆日志明细表
     * author: xiaodi
     * date: 2019/4/01 14:43
     * @param DateRangeRequest $request
     * @param LoginLog $loginLog
     * @return \Dingo\Api\Http\Response
     */
    public function list(DateRangeRequest $request,LoginLog $loginLog)
    {
        $this->check($this->company_id);
        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        $lists=$loginLog->getDetailByDate($this->company_ids,$start,$end)->paginate($this->pagesize);
        $transformer=new LoginLogTransformer();
        return $this->response->paginator($lists,$transformer)->addMeta('colums',$transformer->columns());
    }
}
