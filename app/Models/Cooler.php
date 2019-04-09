<?php

namespace App\Models;

use App\Models\Reports\StatCooler;
use App\Traits\ControllerDataRange;

class Cooler extends Coldchain2Model
{

    use ControllerDataRange;
    public $default_date = '今日';

    protected $table = 'cooler';
    protected $primaryKey = 'cooler_id';
    protected $fillable = ['cooler_id', 'cooler_sn', 'cooler_name', 'cooler_type', 'cooler_img', 'cooler_brand', 'cooler_size', 'cooler_size2', 'cooler_model', 'is_medical', 'door_type', 'cooler_starttime', 'cooler_fillingtime', 'category_id', 'company_id', 'update_time', 'install_time', 'install_uid', 'uninstall_time', 'collector_num', 'come_from', 'status', 'sort'];

    const 状态_正常 = 1;
    const 状态_维修 = 2;
    const 状态_备用 = 3;
    const 状态_报废 = 4;
    const 状态_盘苗 = 5;
    const 状态_除霜 = 6;
    const STATUS = [
        '1' => '正常',
        '2' => '维修', //不报警
        '3' => '备用', //不报警，要显示温度
        '4' => '报废', //不报警，解除sensor绑定
        '5' => '盘苗',
        '6' => '除霜',
    ];
    const 设备类型_冷藏冰箱 = 1;
    const 设备类型_冷冻冰箱 = 2;
    const 设备类型_普通冰箱 = 3;
    const 设备类型_深低温冰箱 = 4;
    const 设备类型_冷藏冷库 = 5;
    const 设备类型_冷冻冷库 = 6;
    const 设备类型_房间室温 = 8;
    const 设备类型_培养箱 = 9;
    const 设备类型_阴凉库 = 10;
    const 设备类型_常温库 = 11;
    const 设备类型_保温箱 = 100;
    const 设备类型_冷藏车 = 101;
    const COOLER_TYPE = [
        '1' => '冷藏冰箱',
        '2' => '冷冻冰箱',
        '3' => '普通冰箱(冷藏+冷冻)',
        '4' => '深低温冰箱',
        '5' => '冷藏冷库',
        '6' => '冷冻冷库',
        '8' => '房间室温',
        '9' => '培养箱',
        '10' => '阴凉库',
        '11' => '常温库',
        '100' => '移动保温箱',
        '101' => '冷藏车',
    ];

    function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    function collectors()
    {
        return $this->hasMany(Collector::class, 'cooler_id', 'cooler_id');
    }

    function collectorsOnline()
    {
        return $this->hasMany(Collector::class, 'cooler_id', 'cooler_id')->where('status', Collector::状态_正常)->orderBy('collector_name', 'asc');
    }

    function collectorsTempTypeError()
    {
        return $this->hasMany(Collector::class, 'cooler_id', 'cooler_id')->where('status', Collector::状态_正常)->where('temp_type', Collector::温区_未知);
    }

    function history($start_time, $end_time)
    {
        $cooler = $this;
        if ($cooler->uninstall_time > 0 and $cooler->uninstall_time < $end_time) {
            $end_time = $cooler->uninstall_time;
        }
        if ($cooler->install_time > 0 and $cooler->install_time > $start_time) {
            $start_time = $cooler->install_time;
        }

        foreach ($cooler->collectors as $key => &$collector) {
            $_start_time = $start_time;
            $_end_time = $end_time;
            if ($collector->uninstall_time > 0 and $collector->uninstall_time < $_end_time) {
                $_end_time = $collector->uninstall_time;
            }
            if ($collector->install_time > 0 and $collector->install_time > $_start_time) {
                $_start_time = $collector->install_time;
            }

            if ($_start_time > $_end_time) {
                unset($cooler->collectors[$key]);
            } else {
                $collector->history = $collector->history($_start_time, $_end_time);
            }

        }

        return $cooler;
    }

    function sensors()
    {
        return $this->hasMany(Collector::class, 'cooler_id', 'cooler_id')->where(['status' => 1])
            ->field('*,collector_name as sensor');
    }

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
//        return $this->belongsTo(Company::class,'company_id','id')->field('id,title,short_title');
    }

    public function getCoolerSizeAttr($value)
    {
        return $value ? $value . 'L' : '-';
    }

    public function getCoolerSize2Attr($value)
    {
        return $value ? $value . 'L' : '-';
    }


    /**
     * @param array $map
     * @param array $where
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function list_all($map = [], $where = [])
    {
        if ($map == []) {
            $map['c.status'] = array('egt', 0);
            $map['b.status'] = 1;
            $map['a.status'] = array('not in', '3,4');
            $map['a.cooler_type'] = array('lt', '100');  //固定式设备
        }
        if ($where) {
            $map += $where;
        }
        $coolers = $this
            ->field('a.cooler_name,a.cooler_sn,a.cooler_id,a.collector_num,a.company_id,b.temp_fix,b.collector_id,b.collector_name,b.supplier_collector_id,a.status as cooler_status,a.cooler_type, c.title as category_name,c.sort as category_sort,b.`temp`,b.humi,b.warning_status,b.warning_type,b.refresh_time,b.temp_fix,b.humi_fix,d.pid,b.note,b.temp_type,ifnull(s.temp_high,999) as temp_high,ifnull(s.temp_low,-999) as temp_low')
            ->alias('a')
            ->join('__COOLER_CATEGORY__ c', ' a.category_id = c.id', 'RIGHT')
            ->join('__COLLECTOR__ b ', ' b.cooler_id = a.cooler_id', 'LEFT')
            ->join('__USER_COMPANY__ d ', ' a.company_id = d.id', 'LEFT')
            ->join('__WARNING_SETTING__ s ', ' s.collector_id = b.collector_id and s.temp_warning=1 and s.status=1', 'LEFT')
            ->where($map)->order('d.cdc_level asc,d.sort desc,d.company_type asc,c.sort asc,c.id asc,a.sort asc,a.cooler_type asc,a.cooler_name asc,a.cooler_id desc,b.collector_name asc')->select();
        //temp_fix 温度偏移修正
        foreach ($coolers as &$vo) {
            $vo['temp'] += $vo['temp_fix'];
            $vo['humi'] += $vo['humi_fix'];
        }
        return $coolers;


    }

    public function getListByCompanyIdsAndMonth($companyIds, $month_start, $month_end)
    {
        return $this->whereIn('company_id', $companyIds)
            ->whereRaw('((uninstall_time = 0 ) or uninstall_time >' . \App\Utils\time_clock(0, date('Y-m-d', $month_start)) . ')and (install_time is NULL or install_time=0 or  install_time <' . \App\Utils\time_clock(24, date('Y-m-d', $month_end)) . ')')
            ->orderBy('sort', 'desc')
            ->orderBy('category_id', 'asc')
            ->orderBy('cooler_id', 'desc');
    }

    public function statCooler()
    {

        return $this->hasMany(StatCooler::class, 'cooler_id', 'cooler_id');
    }


}
