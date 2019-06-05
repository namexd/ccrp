<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Requests\Api\Ccrp\Report\MonthRequest;
use App\Models\Ccrp\Company;
use App\Transformers\Ccrp\Reports\DevicesStatisticTransformer;
use App\Models\Ccrp\Cooler;
use App\Transformers\Ccrp\CoolerTransformer;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use App\Models\Ccrp\Reports\StatMange;
use App\Transformers\Ccrp\Reports\StatManageTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Input;

/**
 * 设备报表
 * Class DevicesController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class DevicesController extends Controller
{
    /**
     * 冷链设备一览表
     * @return Response
     */
    public function statistic()
    {
        $this->check($this->company_id);//支持URL中的?company_id=xxx的子单位的查询
        $companies = Company::whereIn('id', $this->company_ids)
            ->where('status', 1)//状态开启
            ->where('cdc_admin', 0)//使用的单位，非管理单位
            ->paginate($this->pagesize);
        $transfer = new DevicesStatisticTransformer();
        return $this->response->paginator($companies, $transfer)
            ->addMeta('columns', $transfer->columns());
    }

    /**
     * note：冷链管理评估表
     * author: xiaodi
     * date: 2019/3/26 13:43
     * @param MonthRequest $request
     * @return Response
     */
    public function statManage(MonthRequest $request)
    {
        $this->check($this->company_id);
        $date = Input::get('month')??date('Y-m');
        $dateArr = explode('-', $date);
        $year = $dateArr[0];
        $month = $dateArr[1];
        $stat_manages = StatMange::whereIn('company_id', $this->company_ids)
            ->where('year', $year)
            ->where('month', $month)
            ->paginate($this->pagesize);
        $transfer = new StatManageTransformer();
        return $this->response->paginator($stat_manages, $transfer)
            ->addMeta('columns', $transfer->columns());
    }

    /**
     * note：冷链设备评估表
     * author: xiaodi
     * date: 2019/3/26 13:59
     * @param MonthRequest $request
     * @return Response
     */
    public function statCooler(MonthRequest $request)
    {
        $this->check($this->company_id);
        $date =Input::get('month')??date('Y-m', strtotime('-1 Month'));
        $month_first = date('Y-m-01 00:00:00', strtotime($date));
        $month_last = date('Y-m-d H:i:s', strtotime(date('Y-m-01', strtotime($month_first)) . ' +1 month') - 1);;
        $month_start = strtotime($month_first);
        $month_end = strtotime($month_last);
        $stat_manages = (new Cooler())->getListByCompanyIdsAndMonth($this->company_ids, $month_start, $month_end)->paginate($this->pagesize);;
        return $this->response->paginator($stat_manages, new CoolerTransformer())
            ->addMeta('columns', (new StatCoolerTransformer)->columns());
    }

}
