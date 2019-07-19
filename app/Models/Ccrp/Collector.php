<?php

namespace App\Models\Ccrp;


use function App\Utils\abs2;
use function App\Utils\format_value;

/**
 * Class Collector
 * @package App\Models
 */
class Collector extends Coldchain2ModelWithTimestamp
{
    protected $table = 'collector';
    protected $primaryKey = 'collector_id';

    protected $fillable = ['collector_id', 'supplier_id', 'collector_name', 'cooler_id', 'cooler_name', 'supplier_product_model', 'supplier_collector_id', 'category_id', 'company_id', 'temp_warning', 'install_uid', 'humi_warning', 'volt_warning', 'temp', 'humi', 'volt', 'rssi', 'update_time', 'install_time', 'uninstall_time', 'status'];

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
    const 预警类型_离线 = 3;

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
         if($rs and $rs[0]->to_regclass == null)
         {
             $update_index = "DO $$
BEGIN
IF to_regclass('sensor.idx_sensor_".$sn."_collect_time') IS NULL THEN
    CREATE INDEX idx_sensor_".$sn."_collect_time ON \"sensor\".\"".$sn."\" (sensor_collect_time);
END IF;
END$$;";
             $rs = \DB::connection('dbhistory')->select($update_index);
         }

        return $history->setTable('sensor.' . $sn . '')->whereBetween('sensor_collect_time', [$start_time, $end_time])->select(['data_id', 'temp', 'humi', 'sensor_collect_time as collect_time', 'system_time'])->limit(3000)->orderBy('sensor_collect_time', 'asc')->get();
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
        if(array_get($attributes,'supplier_product_model')=='LWTG310S' or array_get($attributes,'supplier_product_model')=='LWTGD310S'){
            $attributes['supplier_id'] = 1001;
        }
        $rs = parent::create($attributes);
        return $rs;
    }
    public function update(array $attributes = [], array $options = [])
    {
        $collector_id = $this->collector_id;
        $save = parent::update($attributes,$options) ;
        $self = $this->find($collector_id);
        if($self['supplier_product_model']=='LWTG310S' or $self['supplier_product_model']=='LWTGD310S'){
            $gateway = new GatewaybindingdataModel();
            $gateway->set_collector($collector_id);
            if($collector_id){
                if($self['status']==1)   //status==2，报废，解除不添加
                {
                    $gateway->do_mod_collector(array('DisplayName'=>$self['collector_name']));

                }else{
                    $gateway->do_del_collector();
                }

            }
        }
        return $save;
    }

}
