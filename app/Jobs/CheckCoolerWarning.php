<?php

namespace App\Jobs;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
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

    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $activeCollectors = Collector::selectRaw('company_id,cooler_id,
        count(collector_id) as total_collector,
        SUM(IF(TIMESTAMPDIFF(MINUTE,from_unixtime(refresh_time),now())>offline_span,1,0)) as offline_collector
        ')
            ->where('status', Collector::状态_正常)
            ->whereHas('cooler', function ($query) {
                $query->where('status', Cooler::状态_正常)->whereIn('cooler_type', [Cooler::设备类型_冷藏冰箱, Cooler::设备类型_冷冻冰箱, Cooler::设备类型_普通冰箱, Cooler::设备类型_深低温冰箱, Cooler::设备类型_冷藏冷库]);
            })
            ->where('company_id', $this->company_id)
            ->where('offline_check', 1)
            ->groupBy('cooler_id')
            ->with(['cooler.coolerWarningTempLogs'])
            ->get();
        $warning_cooler = collect();
        foreach ($activeCollectors as $activeCollector) {
            $activeCooler = $activeCollector->cooler;
            if ($activeCollector->total_collector == $activeCollector->offline_collector) {//所有探头都离线
                $temp = collect($activeCooler->coolerWarningTempLogs)->last();
                if ($temp) {
                    if (Carbon::now() >= Carbon::parse($temp->warning_time)->addDays(1)) {
                        $activeCooler->coolerWarningTempLogs()->create([
                            'warning_time' => Carbon::now(),
                            'company_id' => $activeCollector->company_id
                        ]);
                        //发送冰箱报警
//                        $this->sendMessage($activeCooler, $activeCollector->company_id);
                        $warning_cooler->push($activeCooler);
                    }
                } else {
                    $activeCooler->coolerWarningTempLogs()->create([
                        'warning_time' => Carbon::now(),
                        'company_id' => $activeCollector->company_id
                    ]);
                    $warning_cooler->push($activeCooler);
                }
            } else {
                if ($activeCooler->coolerWarningTempLogs->isNotEmpty()) {//有一个恢复了就把临时日志删除
                    $activeCooler->coolerWarningTempLogs()->delete();
                }
            }
        }
        //发送冰箱报警
        if (count($warning_cooler) > 0) {
            $this->sendMessage($warning_cooler, $this->company_id);

        }
    }

    public function sendMessage($activeCoolers, $company_id)
    {
        $coolers = [];
        $phones = '';
        foreach ($activeCoolers as $activeCooler) {
            $notice_collector = $activeCooler->collectorsOnline->first();
            if ($notice_collector->warningSetting) {
                $coolers[] = $activeCooler->cooler_name;
                $phones .= $notice_collector->warningSetting->warninger->warninger_body;
            }
        }
        $phones_unique = collect(explode(',', $phones))->unique();
        $phones_unique_string = implode(',', $phones_unique->toArray());
        $cooler_names = implode('、', $coolers);
        $messages = [
            'eventcontent' => $cooler_names.'的监测设备全部离线',
        ];;
        $params = [
            'phone' => $phones_unique_string,
            'data' => json_encode($messages),
        ];
        $logs = [
            'event_id' => 0,
            'event_type' => '离线报警',
            'event_value' => 0,
            'event_level' => '',
            'msg_type' => 1,
            'send_to' => $phones_unique_string,
            'send_time' => time(),
            'send_content' => json_encode($messages,JSON_UNESCAPED_UNICODE ),
            'send_content_all' => '【'.env('ALIYUN_SMS_SIGN_NAME').'】'.$cooler_names.'的监测设备全部离线，请及时处理!',
            'collector_name' => '-',
            'cooler_name' => $cooler_names,
            'send_status' => 1,
            'sent_again' => 0,
            'company_id' => $company_id,
            'from_source' => get_client_ip(),
        ];
        WarningSendlogChange::create($logs);
        dispatch(new PushMessage($params));
    }

}
