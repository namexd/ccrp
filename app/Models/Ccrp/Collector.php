<?php

namespace App\Models\Ccrp;


use function App\Utils\abs2;
use function App\Utils\dateFormatByType;
use function App\Utils\format_value;
use Carbon\Carbon;

/**
 * Class Collector
 * @package App\Models
 */
class Collector extends Coldchain2ModelWithTimestamp
{
    protected $table = 'collector';
    protected $primaryKey = 'collector_id';

    protected $fillable = ['collector_id', 'supplier_id', 'collector_name', 'cooler_id', 'cooler_name', 'supplier_product_model', 'supplier_collector_id', 'category_id', 'company_id', 'temp_warning', 'install_uid', 'humi_warning', 'volt_warning', 'temp', 'humi', 'volt', 'rssi', 'update_time', 'install_time', 'uninstall_time', 'status', 'offline_span', 'offline_check', 'temp_type'];

    //探头监测类型：
    const 离线时间 = 3600;
    //探头监测类型：
    const 温区_未知 = 0;
    const 温区_冷藏 = 1;
    const 温区_冷冻 = 2;
    const 温区_室温 = 3;
    const TMEP_TYPE = [
        0 => '未知',
        1 => '冷藏',
        2 => '冷冻',
        3 => '室温',
        4 => '温箱'
    ];
    const 状态_禁用 = 0;
    const 状态_正常 = 1;
    const 状态_报废 = 2;
    const STATUS = [
        '0' => '禁用',
        '1' => '正常',
        '2' => '报废',
    ];

    const 状态禁用 = 0;
    const 状态正常 = 1;
    const 状态报废 = 2;
    const STATUSES = [
        self::状态禁用 => '禁用',
        self::状态正常 => '正常',
        self::状态报废 => '报废',
    ];

    const 离线预警关闭 = 0;
    const 离线预警开启 = 1;
    const OFFLINE_STATUS = [
        self::离线预警关闭 => '关闭',
        self::离线预警开启 => '开启',
    ];
    const 预警状态_正常 = 0;
    const 预警状态_高温 = 1;
    const 预警状态_低温 = 2;
    const WARNING_TYPE = [
        '0' => '正常',//正常
        '1' => '高温',
        '2' => '低温',
//        '3'=>'高湿',
//        '4'=>'低湿',
//        '5'=>'断电',
//        '6'=>'电压低',
//        '7'=>'电压高',
    ];

    const 预警类型正常 = 0;
    const 预警类型高温 = 1;
    const 预警类型低温 = 2;
    const 预警类型高湿 = 3;
    const 预警类型低湿 = 4;
    const 预警类型断电 = 5;
    const 预警类型低电压 = 6;
    const 预警类型高电压 = 7;


    public static $warning_type = [
        '0' => '正常',//正常
        '1' => '高温',
        '2' => '低温',
//        '3'=>'高湿',
//        '4'=>'低湿',
//        '5'=>'断电',
//        '6'=>'电压低',
//        '7'=>'电压高',
    ];

    //探头电压
    const  COLLECTOR_WORRY_VOLT = [
        'ZKS_S1_COOL' => 2.8,
        'ZKS_S1_COLD' => 2.6,
        'ZKS_S2' => 3.8,
    ];
    const 预警类型_离线 = 3;

    const SUPPLIER_PRODUCT_MODEL = [
        'LWTG310' => 'LWTG310 无线温湿度探头',
        'LWTG310S' => 'LWTG310S 无线温湿度探头',
        'LWTGD310' => 'LWTGD310 无线深低温探头',
        'LWYL201' => 'LWYL201 便携式温度记录仪',
        'TG100-AU' => 'TG100-AU 无线温度探头',
        'LWSSH200' => 'LWSSH200 有线温度探头',
        'LWSSH400' => 'LWSSH400 有线温度探头',
        'RCW1000' => 'RCW1000 有线温度探头',
        'LWJC2000' => 'LWJC2000 远距离无线温湿度探头',
        'SANY001' => 'SANY001 海珠三洋探头',
        'LDS511' => 'LDS511 无线温湿度探头',
        'LDS520' => 'LDS520 无线低温探头',
    ];
    function cooler()
    {
        return $this->belongsTo(Cooler::class, 'cooler_id', 'cooler_id');
    }

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }


    function warningSetting()
    {
        return $this->hasOne(WarningSetting::class, 'collector_id', 'collector_id')->where('status', 1);
    }

    function warningEvent()
    {
        return $this->hasMany(WarningEvent::class);
    }

    public function getTempAttribute($value)
    {
        return format_value($value);
    }

    public function getHumiAttribute($value)
    {
        return format_value($value);
    }

    public function getVoltAttribute($value)
    {
        return format_value($value);
    }

    public function getWarningTypeAttr($value)
    {
        return self::$warning_type[$value];
    }

    public function getStatusAttr($value)
    {
        return self::STATUSES[$value];
    }
    public function getUnnormalStatusAttribute()
    {
        if ($this->warning_status == 3) {
            $rs = '离线';
        } elseif ($this->warning_type == 1) {
            $rs = '超温';
        } elseif ($this->warning_type == 2) {
            $rs = '超温';
        } else {
            $rs = '';
        }
        return $rs;
    }

    public function getWarningSettingTempRangeAttribute()
    {
        if ($setting = $this->warningSetting) {
            if ($this->warningSetting->status == 1 and $this->warningSetting->temp_warning == 1) {
                return [$setting->temp_low, $setting->temp_height];
            }
        }
        return [-999, 999];
    }

    /**
     * @param null $start_time
     * @param null $end_time
     * @return DataHistory
     */
    function history($start_time = null, $end_time = null)
    {
        if ($start_time !== null and $end_time === null) {
            $end_time = $end_time ?? strtotime(date('Y-m-d 23:59:59', $start_time));
        } elseif ($start_time === null and $end_time === null) {
            $start_time = $start_time ?? strtotime(date('Y-m-d 00:00:00', time()));
            $end_time = $end_time ?? strtotime(date('Y-m-d 23:59:59', time()));
        }
        $history = new DataHistory();
        $sn = str_replace('-', '', $this->supplier_collector_id);
        $history->tableName($sn);


        $check = "select to_regclass('sensor.idx_sensor_".$sn."_collect_time');";
        $rs = \DB::connection('dbhistory')->select($check);
        if ($rs and $rs[0]->to_regclass == null) {
            $update_index = "DO $$
BEGIN
IF to_regclass('sensor.idx_sensor_".$sn."_collect_time') IS NULL THEN
    CREATE INDEX idx_sensor_".$sn."_collect_time ON \"sensor\".\"".$sn."\" (sensor_collect_time);
END IF;
END$$;";
            $rs = \DB::connection('dbhistory')->select($update_index);
        }

        return $history->setTable('sensor.'.$sn.'')->whereBetween('sensor_collect_time', [$start_time, $end_time])->select(['data_id', 'temp', 'humi', 'sensor_collect_time as collect_time', 'system_time'])->limit(3000)->orderBy('sensor_collect_time', 'asc')->get();
    }

    public function uninstall($collector_id, $note = '')

    {
        $collector = $this->find($collector_id);
        if (!$collector) return false;

        //添加LOG
        $log = $collector->toArray();
        $log['change_time'] = time();
        $log['change_option'] = 1;
        $log['change_note'] = $note;
        CollectorChangeLog::create($log);

        $set['supplier_collector_id'] = '-'.abs2($collector['supplier_collector_id']);
        $set['status'] = 2;
        $set['uninstall_time'] = time();
        $id = $collector->update($set);
        if ($id) {
            (new Cooler)->flush_collector_num($collector['cooler_id']);
            //更新报警器，自动解除已绑定的探头
            $where = 'FIND_IN_SET("'.$collector_id.'", collector_id)';
            $model_ledspeaker = new Ledspeaker();
            $ledspeakers = $model_ledspeaker->whereRaw($where)->get();
            foreach ($ledspeakers as $vo) {
                $collector = $vo['collector_id'];
                $collector = explode(',', $collector);
                $key = array_search($collector_id, $collector);
                if ($key !== false)
                    array_splice($collector, $key, 1);
                $vo['collector_num'] = count($collector);
                $collector = implode(',', $collector);
                $vo['collector_id'] = $collector;
                $vo->save();
            }
            //设置报警设置状态为2  报废
            $collector->warningSetting()->update(['status' => 2]);


        }
        return $id;
    }

    public function create(array $attributes = [], array $options = [])
    {
        if (array_get($attributes, 'supplier_product_model') == 'LWTG310S' or array_get($attributes, 'supplier_product_model') == 'LWTGD310S') {
            $attributes['supplier_id'] = 1001;
        }
        $rs = parent::create($attributes);
        return $rs;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $collector_id = $this->collector_id;
        $save = parent::update($attributes, $options);
        $self = $this->find($collector_id);
        if ($self['supplier_product_model'] == 'LWTG310S' or $self['supplier_product_model'] == 'LWTGD310S') {
            $gateway = new GatewaybindingdataModel();
            $gateway->set_collector($collector_id);
            if ($collector_id) {
                if ($self['status'] == 1)   //status==2，报废，解除不添加
                {
                    $gateway->do_mod_collector(array('DisplayName' => $self['collector_name']));

                } else {
                    $gateway->do_del_collector();
                }

            }
        }
        return $save;
    }

    public function getCollectorByCoolerType(array $type)
    {
        $list = [];
        $results = $this->whereIn('cooler_type', $type)->get();
        foreach ($results as $result) {
            $list[] = [
                'value' => $result->collector_id,
                'label' => $result->collector_name,
            ];
        }
        return $list;
    }

    public static function lists_warning_type()
    {
        return [
            'list' => array2list(self::$warning_type),
            'default' => array2keys(self::$warning_type) //所有状态
        ];
    }

    function certifications()
    {
        return $this->hasManyThrough(Certification::class, CollectorHasCertification::class, 'collector_id', 'id', 'collector_id');
    }

    public function checkOfflineRate80($company_id = null)
    {
        if ($company_id == null) {
            $query = $this->whereHas('cooler', function ($query) {
                $query->where('status', '<>', Cooler::状态_备用);
            })->whereNotIn('company_id', Company::getUnwatchIds())->where('status', 1)->selectRaw('"company_id" as object_key,company_id as object_value,concat(round(sum(IF(warning_status ="3", 1, 0))/count(collector_id),2)*100,\' % \') as result')->groupBy('company_id')->havingRaw('(sum(IF(warning_status ="3", 1, 0))/(count(collector_id)))>?', [0.8])->get();
            return $query;
        } else {
            $query = $this->whereHas('cooler', function ($query) {
                $query->where('status', '<>', Cooler::状态_备用);
            })->where('status', 1)->selectRaw('"company_id" as object_key,company_id as object_value,concat(round(sum(IF(warning_status ="3", 1, 0))/count(collector_id),2)*100,\' % \') as result')->where('company_id', $company_id)->groupBy('company_id')->first();
            return $query;
        }

    }

//巡检报告-探头数量
    public function getCollectorCount($company_id, $date = '')
    {

        $start=Carbon::createFromTimestamp($date['end'])->startOfDay()->timestamp;
        $company_ids = Company::find($company_id)->ids(0);
        return $this
            ->whereRaw('((uninstall_time = 0 ) or uninstall_time >' . $start . ') and (install_time is NULL or install_time=0 or  install_time <' . $date['end'] . ')')
            ->whereIn('company_id', $company_ids)
            ->count();
    }

    //巡检报告-监测设备维护统计表（新增）
    public function getAddCollector($company_id, $date = '')
    {

        $company_ids = Company::find($company_id)->ids(0);
        $bfIds=CollectorChangelog::where('change_option',0)->pluck('new_supplier_collector_id');
        return $this
            ->whereBetween('install_time', [$date['start'], $date['end']])
            ->whereRaw('((install_time-uninstall_time)>0)')
            ->whereIn('company_id', $company_ids)
            ->whereNotIn('supplier_collector_id',$bfIds)
            ->with(['company' => function ($query) {
                $query->selectRaw('id,title');
            },'cooler'=>function($query){
                $query->selectRaw('cooler_id,cooler_sn');
            }])
            ->selectRaw('cooler_id,company_id,install_time,supplier_collector_id')
            ->get()
            ->toArray();
    }

    //巡检报告-启用冷链装备报警未开启清单
    public function getWarningUnableCollector($company_id, $date = '')
    {
        $company_ids = Company::find($company_id)->ids(0);
        return $this->whereHas('warningSetting', function ($query) {
            $query->where('status', '<>', 1)->orWhere('temp_warning', '<>', 1);
        })->where('status', 1)
            ->whereIn('company_id', $company_ids)
            ->with(['company' => function ($query) {
                $query->selectRaw('id,title');
            },'cooler'=>function($query){
                $query->selectRaw('cooler_id,cooler_name,cooler_sn,status');
            }])
            ->selectRaw('cooler_id,company_id,collector_name,supplier_collector_id')
            ->get()
            ->toArray();
    }

    //巡检报告-探头电压异常清单
    public function getPowerUnusualCollector($company_id, $date = '')
    {
        $company_ids = Company::find($company_id)->ids(0);

        $productmodel=new Product();
        $prifix=$productmodel->getConnection()->getConfig('prefix');
        return $this->join($productmodel->getTable(),function ($join) use ($productmodel){
            $join->on($this->getTable().'.supplier_product_model','=',$productmodel->getTable().'.supplier_product_model');
        })->whereRaw($prifix.$this->getTable().'.status=1 and volt>=0 and  (temp_type!=2 and volt <= '.$prifix.$productmodel->getTable().'.safe_collector_volt_low or (temp_type=2 and volt <= '.$prifix.$productmodel->getTable().'.cold_safe_collector_volt_low))')
            ->whereIn('company_id',$company_ids)
            ->with(['company'=>function($query){
                $query->selectRaw('id,title');
            },'cooler'=>function($query){
                $query->selectRaw('cooler_id,cooler_sn');
            }])
            ->selectRaw('temp_type,company_id,cooler_id,supplier_collector_id,volt,'.$prifix.$productmodel->getTable().'.safe_collector_volt_low,'.$prifix.$productmodel->getTable().'.cold_safe_collector_volt_low')
            ->get()
            ->toArray();
//            ->toSql();
    }
}
