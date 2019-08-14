<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Cooler;
use App\Traits\ModelFields;
use Carbon\Carbon;

class StatCooler extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'stat_cooler';

    public function cooler()
    {

        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }

    static public function fieldTitles()
    {
        return [
            'month' => '年月',
            'temp_avg'=>'平均温度',
            'temp_high'=>'最高温度',
            'temp_low'=>'最低温度',
            'error_times'=>'设备故障次数',
            'warning_times'=>'超温预警次数',
            'temp_variance'=>'冷链设备评估值',
        ];
    }
    public function getUnusualEvaluates($company_id,$quarter='')
    {
        $month=Carbon::now()->subQuarter(1)->endOfQuarter()->format('Y-m');
        $company_ids=Company::find($company_id)->ids(0);
        return $this->whereHas('cooler',function($query) use ($company_ids){
            $query->whereIn('company_id',$company_ids)->select('cooler_id');
        })->with(['cooler'=>function($query){
            $query->selectRaw('company_id,cooler_id,cooler_name,cooler_type,cooler_brand,cooler_model,status')
                ->with(['company'=>function($query){
                    $query->selectRaw('id,title');
                }]);
        }])->whereRaw('temp_variance>=5 and month="'.$month.'"')
            ->selectRaw('cooler_id,temp_avg,temp_high,temp_low,temp_variance,warning_times,error_times')
            ->get()
            ->toArray();
    }

    public function getListByMonths($cooler_ids, $start, $end)
    {
        $coolerIds=Cooler::whereHas('collectors',function ($query){
            $query->whereIn('temp_type',[1,2]);
        })->whereIn('cooler_id',$cooler_ids)->pluck('cooler_id');
        $start = Carbon::createFromTimestamp(strtotime($start));
        $end = Carbon::createFromTimestamp(strtotime($end));
        $start_month = $start->firstOfMonth()->timestamp;
        $end_month = $end->endOfMonth()->timestamp;
        $result = $this->selectRaw('id,cooler_id,ROUND(avg(temp_avg),2) as temp_avg,MAX(temp_high) as temp_high,MIN(temp_low) as temp_low,ROUND(avg(temp_variance),2) as temp_variance,sum(error_times) as error_times')->whereRaw('(CONVERT((UNIX_TIMESTAMP(concat(month,"-01"))),SIGNED) between '.$start_month.' and '.$end_month.')')->whereIn('cooler_id', $coolerIds)->groupBy('cooler_id')->with(['cooler.company'])->get();
        return $result;

    }
}
