<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Requests\Api\Ccrp\Report\DateRangeRequest;
use App\Http\Requests\Api\Ccrp\Report\WarningOverRunRequest;
use App\Models\Ccrp\Warninger;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
use App\Models\Ccrp\WarningSendlog;
use App\Transformers\Ccrp\CompanyTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;


/**
 * 预警历史统计表
 * Class WarningersController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class WarningersController extends Controller
{
    /**
     * note：预警历史统计表
     * author: xiaodi
     * date: 2019/4/01 9:55
     * @param DateRangeRequest $request
     * @param Warninger $warninger
     * @return \Dingo\Api\Http\Response
     */
    public function statistics(DateRangeRequest $request, Warninger $warninger)
    {
        $type = $request->get('type') ?? 1;
        $todayTime = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 24 * 3600 - 1;
        if ($request->get('start'))
            $start = strtotime(str_replace('+', ' ', $request->get('start')));
        else
            $start = mktime(0, 0, 0, date('m'), '1', date('Y'));//1号

        if ($request->get('end'))
            $end = strtotime(str_replace('+', ' ', $request->get('end')));
        else
            $end = $todayTime;

        $this->check($this->company_id);
        $result = $warninger->getHistoryList($this->company_ids, $start, $end, $type)->paginate($this->pagesize);
        return $this->response->paginator($result, new CompanyTransformer())->addMeta('columns', $warninger->colums($type));
    }

    /**
     * note:报警类型统计表（昨日）
     * author: xiaodi
     * date: 2019/6/24 16:04
     * @param WarningEvent $warningEvent
     * @param WarningSendlog $sendlog
     * @param WarningSenderEvent $event
     * @return mixed
     */
    public function warningTypeStatistics(WarningEvent $warningEvent, WarningSendlog $sendlog, WarningSenderEvent $event)
    {
        $this->check();
//        if (!$result=\Cache::get('warning_type_statistics'.$this->company->id))
//        {
            $result['data']['temp'] = $warningEvent->getYesterdayTempStat($this->company_ids);
            $result['data']['power_off'] = $event->getYesterDayPowerOff($this->company_ids);
            $result['data']['off_line'] = $sendlog->getYesterdayOffLineCount($this->company_ids);
//            \Cache::put('warning_type_statistics'.$this->company->id,$result,Carbon::today()->endOfDay());
//        }
        return $this->response->array($result);
    }

    public function cooler_temp_overrun(WarningOverRunRequest $request, Warninger $warninger)
    {
        $todayTime = mktime(0, 0, 0, date('m'), date('d'), date('Y')) + 24 * 3600 - 1;
        if ($request->get('start'))
            $start = strtotime(str_replace('+', ' ', $request->get('start')));
        else
            $start = mktime(0, 0, 0, date('m'), '1', date('Y'));//1号

        if ($request->get('end'))
            $end = strtotime(str_replace('+', ' ', $request->get('end')));
        else
            $end = $todayTime;
        $cooler_id = $request->get('cooler_id');
        $this->check($this->company_id);
        $result = $warninger->getOverRunList($cooler_id, $start, $end)->paginate($this->pagesize);
        return $this->response->paginator($result, new CompanyTransformer())->addMeta('columns', $warninger->colums);

    }
}
