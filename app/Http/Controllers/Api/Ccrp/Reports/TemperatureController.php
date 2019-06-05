<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;
use App\Http\Requests\Api\Ccrp\Report\DateRangeRequest;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Reports\StatCoolerHistoryTemp;
use App\Models\Ccrp\Reports\TemperatuesCoolerHistory;
use App\Transformers\Ccrp\CoolerTransformer;
use Illuminate\Support\Facades\Input;

/**
 * 温度报表
 * Class TemperatureController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class TemperatureController extends Controller
{
    /**
     * note：温度质量控制表
     * author: xiaodi
     * date: 2019/3/27 9:55
     * @param DateRangeRequest $request
     * @return void
     */
    public function statCoolerHistoryTemp(DateRangeRequest $request)
    {
        $this->check($this->company_id);
        $start = Input::get('start');
        $end = Input::get('end');
        $point = json_decode(Input::get('point'), true);
        $cooler_id = Input::get('cooler_id');
        if (!$cooler_id)
        {
          return  $this->response->errorBadRequest('请选择冷链设备');
        }
        $result['data'] = (new StatCoolerHistoryTemp())->getTemp($start, $end, $point, $cooler_id);
        return $this->response->array($result);
    }

    public function CoolerHistoryList($date)
    {
        $this->check($this->company_id);
        $date = $date??date('Y-m', strtotime('-1 Month'));
        $month_first = date('Y-m-01 00:00:00', strtotime($date));
        $month_last = date('Y-m-d H:i:s', strtotime(date('Y-m-01', strtotime($month_first)) . ' +1 month') - 1);;
        $month_start = strtotime($month_first);
        $month_end = strtotime($month_last);
        $coolers = (new Cooler())->getListByCompanyIdsAndMonth($this->company_ids, $month_start, $month_end)->paginate($this->pagesize);
        $lists=(new  TemperatuesCoolerHistory())->getExportUrl($coolers,$this->user()->id,$date);
        return $this->response->paginator($lists,new CoolerTransformer());
    }

    public function CoolerHistoryShow($cooler_id,$month)
    {
        $data=(new TemperatuesCoolerHistory())->getCoolerHistory30($cooler_id,$month);
        return $this->response->array($data);
    }
}
