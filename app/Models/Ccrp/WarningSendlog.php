<?php

namespace App\Models\Ccrp;

use Carbon\Carbon;

class WarningSendlog extends Coldchain2pgModel
{
    protected $table = 'warning_sendlog';
    protected $fillable = [
        'event_id',
        'event_type',
        'event_value',
        'event_level',
        'msg_type',
        'send_to',
        'send_time',
        'send_content',
        'send_content_all',
        'send_status',
        'send_rst',
        'collector_id',
        'collector_name',
        'cooler_id',
        'cooler_name',
        'sender_id',
        'sent_again',
        'category_id',
        'company_id',
        'from_source',
    ];
    const TIME_FIELD = 'send_time';
    const TYPE = [
        0 => '无',
        1 => '短信',
        2 => '邮件',
        3 => '微信',
    ];
    const 市电断电 = '市电断电';
    const 温度报警 = '温度报警';
    const 离线报警 = '离线报警';
    const EVENT_TYPES = [
        '市电断电' => self::市电断电,
        '温度报警' => self::温度报警,
        '离线报警' => self::离线报警,
    ];
    public function eventOverTemp()
    {
        return $this->belongsTo(WarningEvent::class, 'event_id', 'id')->where('event_type', '温度报警');
    }

    public function eventPoweroff()
    {
        return $this->belongsTo(WarningSenderEvent::class, 'event_id', 'logid')->where('event_type', '断电报警');
    }

    public function collector()
    {
        return $this->belongsTo(Collector::class, 'collector_id', 'collector_id')->where('collector_id', '>', 0);
    }

    public function sender()
    {
        return $this->belongsTo(Sender::class, 'sender_id', 'sender_id')->where('sender_id', '>', 0)->where('status', 1);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function getYesterdayOffLineCount($company_ids)
    {
        $start = Carbon::yesterday()->startOfDay()->timestamp;
        $end = Carbon::yesterday()->endOfDay()->timestamp;
        return $this
            ->where('event_type', self::预警类型_离线)
            ->whereIn('company_id', $company_ids)
            ->whereBetween('send_time', [$start, $end])
            ->count();
    }
}
