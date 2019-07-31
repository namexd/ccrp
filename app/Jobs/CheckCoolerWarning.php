<?php

namespace App\Jobs;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\WarningSendlogChange;
use Carbon\Carbon;
use function EasyWeChat\Kernel\Support\get_client_ip;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckCoolerWarning implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companyIds=Company::whereHas('useSettings',function ($query){
            $query->where('setting_id',Company::单位设置_开启冰箱整体离线巡检)->where('value',1);
        })->pluck('id');
        $activeCollectors = Collector::selectRaw('company_id,cooler_id,
        count(collector_id) as total_collector,
        SUM(IF(TIMESTAMPDIFF(HOUR,from_unixtime(refresh_time),now())>4,1,0)) as offline_collector
        ')
            ->where('status', Collector::状态_正常)
            ->whereIn('company_id', $companyIds)
            ->groupBy('cooler_id')
            ->with(['cooler.coolerWarningTempLogs'])
            ->get();
        foreach ($activeCollectors as $activeCollector) {
            $activeCooler = $activeCollector->cooler;
            if ($activeCollector->total_collector == $activeCollector->offline_collector) {//所有探头都离线
                $temp = collect($activeCooler->coolerWarningTempLogs)->last();
                if ($temp) {
                    if (Carbon::now() >= Carbon::parse($temp->warning_time)->addDays(1)) {
                        //发送冰箱报警
                        $this->sendMessage($activeCooler, $activeCollector->company_id);
                        $activeCooler->coolerWarningTempLogs()->create([
                            'warning_time' => Carbon::now(),
                            'companyIds_id' => $activeCollector->company_id
                        ]);
                    }
                } else {
                    //发送冰箱报警
                    $this->sendMessage($activeCooler, $activeCollector->company_id);
                    $activeCooler->coolerWarningTempLogs()->create([
                        'warning_time' => Carbon::now(),
                        'companyIds_id' => $activeCollector->company_id
                    ]);
                }
            } else {
                if ($activeCooler->coolerWarningTempLogs->isNotEmpty()) {//有一个恢复了就把临时日志删除
                    $activeCooler->coolerWarningTempLogs()->delete();
                }
            }
        }
//        $activeCoolers = $companyIds->coolersOnline;
//        foreach ($activeCoolers as $activeCooler) {
//            $offline = 0;
//            $activeCollectors=$activeCooler->collectorsOnline()->where('offline_check',1)->get();
//            foreach ($activeCollectors as $activeCollector) {
//                if (Carbon::now()->diffInHours(Carbon::createFromTimestamp($activeCollector->refresh_time)) >= 4)//探头离线了
//                {
//                    //统计一下
//                    $offline++;
//                }
//            }
//            if ($offline == $activeCollectors->count()) {//所有探头都离线
//                $temp = $activeCooler->coolerWarningTempLogs->last();
//                if ($temp) {
//                    if (Carbon::now() >= Carbon::parse($temp->warning_time)->addDays(1)) {
//                        //发送冰箱报警
////                        $this->sendMessage($activeCooler, $companyIds);
//                        $activeCooler->coolerWarningTempLogs()->create([
//                            'warning_time' => Carbon::now(),
//                            'companyIds_id'=>$companyIds->id
//                        ]);
//                    }
//                } else {
//                    //发送冰箱报警
////                    $this->sendMessage($activeCooler, $companyIds);
//                    $activeCooler->coolerWarningTempLogs()->create([
//                        'warning_time' => Carbon::now(),
//                        'companyIds_id'=>$companyIds->id
//                    ]);
//                }
//            } else {
//                if ($activeCooler->coolerWarningTempLogs) {//有一个恢复了就把临时日志删除
//                    $activeCooler->coolerWarningTempLogs()->delete();
//                }
//            }
//        }

    }

    public function sendMessage($activeCooler, $companyIds)
    {
        $notice_collector = $activeCooler->collectorsOnline->first();
        $messages = [
            'deviceid' => $activeCooler->cooler_name,
            'alarmvalue' => '检测设备全部离线'
        ];
        $phones = $notice_collector->warningSetting->warninger->warninger_body;
        $params = [
            'phone' => $phones,
            'data' => $messages,
            'template' => ''
        ];
//        dispatch(new PushMessage($params));
        foreach (explode(',', $phones) as $phone) {
            $logs = [
                'event_id' => 0,
                'event_type' => '离线报警',
                'event_value' => 0,
                'event_level' => 0,
                'msg_type' => 1,
                'send_to' => $phone,
                'send_time' => time(),
                'send_content' => json_encode($messages),
                'send_content_all' => '【'.env('ALIYUN_SMS_SIGN_NAME').'】设备'.$activeCooler->cooler_name.'的检测设备全部离线，请及时处理!',
                'collector_name' => '-',
                'cooler_id' => $activeCooler->cooler_id,
                'cooler_name' => $activeCooler->cooler_name,
                'send_status' => 1,
                'sent_again' => 0,
                'companyIds_id' => $companyIds->id,
                'from_source' => get_client_ip(),
            ];
            WarningSendlogChange::create($logs);
        }

    }
}