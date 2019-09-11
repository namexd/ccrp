<?php


namespace App\Models\Ccrp;


use App\Models\Ccrp\Reports\LoginLog;
use App\Models\Ccrp\Reports\StatMange;
use App\Models\Ccrp\Reports\StatManualRecord;
use App\Models\Ccrp\Sys\SysCompanyDetail;
use App\Models\Ccrp\Sys\SysCompanyPhoto;
use App\Models\Ccrp\Sys\SysCoolerDetail;
use App\Models\Ccrp\Sys\SysCoolerPhoto;
use function App\Utils\http;
use Carbon\Carbon;

class CompanyPhysical extends Coldchain2ModelWithTimestamp
{

    protected $fillable = ['company_id', 'physical_id', 'score', 'detail', 'count_time'];


    public function getBindMiniProgram($company_id, $config)
    {
        $score = 0;
        $result = http('GET', config('app.we_url').'/api/get_bind_miniProgram/'.$company_id);
        $result = json_decode($result,true);
        $count = $result['count'];
        if ($count >= 2) {
            $score = 10;
        } elseif ($count == 1) {
            $score = 5;
        }
        return ['score' => $score, 'detail' => '该单位“壹苗链”绑定'.$count.'人'];
    }

    public function getCertification($company_id, $config)
    {
        $score = 0;
        $result = http('GET', config('app.we_url').'/api/get_certification/'.$company_id);
        $result = json_decode($result,true);
        $count = $result['count'];
        if ($count >= 2) {
            $score = 10;
        } elseif ($count == 1) {
            $score = 5;
        }
        return ['score' => $score, 'detail' => '该单位实名认证'.$count.'人'];
    }

    public function getWarningEvent($company_id, $config)
    {
        $count_overtemp = WarningEvent::where('company_id', $company_id)->whereRaw("date_format(warning_event_time, '%Y-%m') = DATE_FORMAT(now(), '%Y-%m')")->count();
        $count_poweroff = WarningSenderEvent::where('company_id', $company_id)->whereRaw("date_format(sensor_event_time, '%Y-%m') = DATE_FORMAT(now(), '%Y-%m')")->count();
        $count_overtemp_handled = WarningEvent::where('company_id', $company_id)->whereRaw("date_format(warning_event_time, '%Y-%m') = DATE_FORMAT(now(), '%Y-%m')")->where('handled', 1)->count();
        $count_poweroff_handled = WarningSenderEvent::where('company_id', $company_id)->whereRaw("date_format(sensor_event_time, '%Y-%m') = DATE_FORMAT(now(), '%Y-%m')")->where('handled', 1)->count();
        $handle_count = $count_overtemp_handled + $count_poweroff_handled;
        $count = $count_overtemp + $count_poweroff;
        $detail='该单位本月总报警数:'.$count.'，已处理报警数:'.$handle_count;
        if ($count==0)
        {
            $detail='该单位本月无报警';
            $score=$config->weight;
        }else
        {
            $score = $handle_count / $count;
            $score = $score * $config->weight;
            $score = intval(floor($score));
        }
        return ['score' => $score, 'detail' =>$detail ];
    }

    public function getCorrectLoginTimes($company_id, $config)
    {
        $totalDays = Carbon::now()->day;
        $logs = (new LoginLog())->getReportByMonth([$company_id], date('Y-m'));
        $count=0;
        $arrays=$logs[0];
        unset($arrays['title']);
        foreach ($arrays as  $array)
        {
          if ($array['AM']==1&&$array['PM']==1)
          {
              $count++;
          }
        }
        $score = $count / $totalDays;
        $score = $score * $config->weight;
        $score = intval(floor($score));
        return ['score' => $score, 'detail' => '该单位本月有效登陆次数:'.$count];
    }


    public function getCorrectSign($company_id, $config)
    {
        $company = Company::find($company_id);
        if ($company->doesManualRecords) {
            $totalDays = Carbon::now()->day;
            $records = StatManualRecord::getListByMonth($company_id);
            $count=0;
            foreach ($records as $record)
            {
                if ($record['AM']>0&&$record['PM']>0)
                {
                    $count++;
                }
            }
            $score = $count / $totalDays;
            $score = $score * $config->weight;
            $score = intval(floor($score));
            $detail = '该单位有效人工签名天数:'.$count;

        } else {
            $score = 0;
            $detail = '该单位未开启人工测温记录功能';
        }

        return ['score' => $score, 'detail' => $detail];
    }

    public function getWarningInfo($company_id, $config)
    {
        $company = Company::find($company_id);
        $collectors = $company->collectors();
        $warning_set_count = WarningSetting::query()
            ->whereIn('collector_id', $collectors->pluck('collector_id'))
            ->where('company_id', $company_id)
            ->where('status', 1)
            ->where('temp_warning', 1)
            ->count();
        $count = count($collectors);
        $score = $warning_set_count / $count;
        $score = $score * $config->weight;
        $score = intval(floor($score));
        return ['score' => $score, 'detail' => '该单位探头报警开启的数量:'.$warning_set_count];

    }

    public function getCompletedCooler($company_id, $config)
    {
        $company = Company::find($company_id);
        $cooler_bx_count=$company->cooler_bx_count();
        $cooler_lk_count=$company->cooler_lk_count();
        $coolers=$company->coolersOnline();
        $detail_count=CoolerDetail::query()->whereIn('cooler_id',$coolers->pluck('cooler_id'))->count();
        $detail_bx_count=SysCoolerDetail::query()->whereRaw(' (locate("冰箱",cooler_type_category) or length(cooler_type_category)=0 or ISNULL(cooler_type_category))')->count();
        $detail_lk_count=SysCoolerDetail::query()->whereRaw(' (locate("冷库",cooler_type_category) or length(cooler_type_category)=0 or ISNULL(cooler_type_category))')->count();
        $cooler_photos=SysCoolerPhoto::query()->count();
        $photo_cooler=CoolerPhoto::query()->whereIn('cooler_id',$coolers->pluck('cooler_id'))->count();
        $completed_count=$detail_count+$photo_cooler;
        $count=($cooler_bx_count*$detail_bx_count)+($cooler_lk_count*$detail_lk_count)+(count($coolers)*$cooler_photos);
        $score = $completed_count / $count;
        return ['score' => intval(floor($score * $config->weight)), 'detail' => '该单位冷链设备资料完成度:'.round($score*100,2).'%'];
    }

    public function getCompletedCompany($company_id, $config)
    {
        $detail_count=CompanyDetail::query()->where('company_id',$company_id)->count();
        $photo_count=CompanyPhoto::query()->where('company_id',$company_id)->count();
        $sys_detail_count=SysCompanyDetail::query()->count();
        $sys_photo_count=SysCompanyPhoto::query()->count();
        $complete_count=$detail_count+$photo_count;
        $count=$sys_detail_count+$sys_photo_count;
        $score = $complete_count / $count;
        return ['score' => intval(floor($score * $config->weight)), 'detail' => '该单位资料完成度:'.round($score*100,2).'%'];

    }
}