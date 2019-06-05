<?php

namespace App\Models\Ccrp;

use App\Traits\ModelFields;
use DB;

class Warninger extends Coldchain2Model
{
    use ModelFields;

    const 预警历史统计表 = 1;
    const 所有报警一览表 = 2;
    const REPOERT_COLUMS = [
        self::预警历史统计表 => [
            'temp_count_all',
            'temp_count_high',
            'temp_count_low',
            'count_temp_warning_30',
            'count_temp_warning_60',
            'count_temp_warning_120',
            'count_temp_warning_longer',
            'count_power_off',
            'count_power_off_30',
            'count_power_off_60',
            'count_power_off_120',
            'count_power_off_longer'
        ],
        self::所有报警一览表 => [
            'count_cooler',
            'count_tem_unhandled',
            'temp_count_all',
            'temp_count_high',
            'temp_count_low',
            'count_power_unhandled',
            'count_power_off',
        ]
    ];

    const 发送类型_短信 = 1;
    const 发送类型_邮件 = 2;
    const 发送类型_微信 = 3;
    const 发送类型_电话 = 4;
    const 发送类型_短信2 = 6;
    const WARNINGER_TYPES = [
        self::发送类型_短信 => '短信',
        self::发送类型_邮件 => '邮件',
        self::发送类型_微信 => '微信',
        self::发送类型_电话 => '电话',
        self::发送类型_短信2 => '短信',
    ];
    protected $table = 'warninger';
    protected $primaryKey = 'warninger_id';
    protected $fillable = ['warninger_id', 'warninger_name', 'warninger_type', 'warninger_type_level2', 'warninger_type_level3', 'warninger_body', 'warninger_body_pluswx', 'warninger_body_level2', 'warninger_body_level2_pluswx', 'warninger_body_level3', 'warninger_body_level3_pluswx', 'using_sensor_num', 'set_time', 'set_uid', 'bind_times', 'category_id', 'company_id'];

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function getHistoryList($companyIds, $start, $end, $type)
    {
        switch ($type) {
            case self::预警历史统计表:
                $result = Company::with(['warning_sender_events' => function ($query) use ($start, $end) {
                    $query->select(DB::raw('company_id,
        sum(IF(warning_type="0", 1, 0)) as count_power_off,
        sum(IF((warning_type="0" and handled=1 and  (handled_time - system_time <= 30*60)), 1, 0)) as count_power_off_30,
        sum(IF((warning_type="1" and handled=1 and  (handled_time - system_time > 30*60) and (handled_time - system_time <= 60*60)), 1, 0)) as count_power_off_60,
        sum(IF((warning_type="0" and handled=1 and   (handled_time - system_time > 60*60) and (handled_time - system_time <= 120*60)), 1, 0)) as count_power_off_120,
        sum(IF((warning_type="0" and (handled = 0 or  (handled_time - system_time > 120*60) )), 1, 0)) as count_power_off_longer'))
                        ->whereBetween('system_time', [$start, $end])->groupBy('company_id');
                }, 'warning_events' => function ($query) use ($start, $end) {
                    $query->select(DB::raw('company_id,
        count(warning_type) as temp_count_all,
        sum(IF(warning_type="1", 1, 0)) as temp_count_high,
        sum(IF(warning_type="2", 1, 0)) as temp_count_low,
        sum(IF( ((warning_type="1" or warning_type="2") and handled=1 and  (handled_time - warning_event_time <= 30*60)), 1, 0)) as count_temp_warning_30,
        sum(IF( ((warning_type="1" or warning_type="2") and handled=1 and  (handled_time - warning_event_time <= 60*60) and (handled_time - warning_event_time > 30*60)), 1, 0)) as count_temp_warning_60,
        sum(IF( ((warning_type="1" or warning_type="2") and handled=1 and  (handled_time - warning_event_time <= 120*60) and (handled_time - warning_event_time > 60*60)), 1, 0)) as count_temp_warning_120,
        sum(IF( ((warning_type="1" or warning_type="2") and (handled=0 or  (handled_time - warning_event_time > 120*60))), 1, 0)) as count_temp_warning_longer'))
                        ->whereBetween('warning_event_time', [$start, $end])->groupBy('company_id');
                }])->whereIn('id', $companyIds);
                break;
            case self::所有报警一览表:
                $result = Company::with(['warning_sender_events' => function ($query) use ($start, $end) {
                    $query->select(DB::raw('company_id,
           SUM(CASE WHEN `handled` = 0 THEN 1 ELSE 0 END ) AS count_power_unhandled,
        sum(IF(warning_type="0", 1, 0)) as count_power_off'))
                        ->whereBetween('system_time', [$start, $end])->groupBy('company_id');
                }, 'warning_events' => function ($query) use ($start, $end) {
                    $query->select(DB::raw('company_id,
        SUM(CASE WHEN `handled` = 0 THEN 1 ELSE 0 END ) AS count_temp_unhandled,
        count(warning_type) as temp_count_all,
        sum(IF(warning_type="1", 1, 0)) as temp_count_high,
        sum(IF(warning_type="2", 1, 0)) as temp_count_low'))
                        ->whereBetween('warning_event_time', [$start, $end])->groupBy('company_id');
                },'coolers'=>function($query) use($start,$end){
                    $query->select(DB::raw('company_id,COUNT(company_id) as count_cooler'))
                    ->whereRaw("(uninstall_time = 0 or uninstall_time >" . $start . ") and (install_time is NULL or install_time=0 or  install_time <" . $end . ")")
                        ->groupBy('company_id');
                }])->whereIn('id', $companyIds);
                break;
        }
        return $result;
    }

    static public function fieldTitles()
    {
        return
            [
                'warninger_name'=>'预警通道名称',
                'warninger_type'=>'预警类型',
                'warninger_body'=>'一级预警设置',
                'warninger_body_level2'=>'二级预警设置',
                'warninger_body_level3'=>'三级预警设置',
                'bind_times'=>'绑定次数',

                'count_tem_unhandled' => '未处理温度预警',
                'temp_count_all' => '温度预警总计',
                'temp_count_high' => '高温预警',
                'temp_count_low' => '低温预警',
                'count_temp_warning_30' => '30分钟内响应',
                'count_temp_warning_60' => '60分钟内响应',
                'count_temp_warning_120' => '120分钟内响应',
                'count_temp_warning_longer' => '120分钟外响应',
                'count_power_unhandled' => '未处理断电预警',
                'count_power_off' => '断电预警总计',
                'count_power_off_30' => '30分钟内响应',
                'count_power_off_60' => '60分钟内响应',
                'count_power_off_120' => '120分钟内响应',
                'count_power_off_longer' => '120分钟外响应',
                'count_cooler' => '冰箱数量'
            ];
    }

    public function colums($type)
    {
        return self::getFieldsTitles(self::REPOERT_COLUMS[$type]);
    }

    public function getWarningerTypeAttribute($value)
    {
        return isset(self::WARNINGER_TYPES[$value])?self::WARNINGER_TYPES[$value]:$value;
    }
}
