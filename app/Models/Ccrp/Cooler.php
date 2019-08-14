<?php

namespace App\Models\Ccrp;

use App\Http\Requests\Api\Ccrp\Setting\CoolerStatusRequest;
use App\Models\Ccrp\Reports\CoolerLog;
use App\Models\Ccrp\Reports\StatCooler;
use App\Models\Ccrp\Sys\SysCoolerDetail;
use App\Models\Ccrp\Sys\SysCoolerPhoto;
use App\Models\CoolerCategory;
use App\Traits\ControllerDataRange;
use App\Traits\ModelFields;
use App\Transformers\Ccrp\CoolerType100Transformer;
use function App\Utils\time_clock;

class  Cooler extends Coldchain2Model
{

    use ModelFields;
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
    const IS_MEDICAL = ['0' => '未知', '1' => '否', '2' => '是'];

    public static $status = [
        '1' => '正常',
        '2' => '维修', //不报警
        '3' => '备用', //不报警，要显示温度
        '4' => '报废', //不报警，解除sensor绑定
        '5' => '盘苗',
        '6' => '除霜',
    ];

    const 设备图片_冷藏冰箱_小型 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_small.png';
    const 设备图片_冷藏冰箱_中型 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_middle.png';
    const 设备图片_冷藏冰箱_大型 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_big.png';
    const 设备图片_普通冰箱 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_ptbx.png';
    const 设备图片_冷冻冰箱 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_ldbx.png';
    const 设备图片_冷藏冷库 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_lclk.png';
    const 设备图片_冷冻冷库 = 'https://cdn-static.coldyun.net/images/ico/icebox/icebox_ldlk.png';
    const 设备图片_其他 = '';

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
    const 设备类型_台式小冰箱 = 12;
    const 设备类型_保温箱 = 100;
    const 设备类型_冷藏车 = 101;
    const COOLER_TYPE = [
        '1' => '冷藏冰箱',
        '2' => '冷冻冰箱',
        '3' => '普通冰箱(冷藏+冷冻)',
        '4' => '深低温冰箱',
        '5' => '冷藏冷库',
        '6' => '冷冻冷库',
        '7' => '冷藏车',
        '8' => '房间室温',
        '9' => '培养箱',
        '10' => '阴凉库',
        '11' => '常温库',
        '12' => '台式小冰箱',
        '13' => '冰衬冰箱',
        '14' => '疫苗运输车',
        '15' => ' 备用冷库制冷机组',
        '16' => '发电机',
        '17' => '冷藏包',
        '18' => '温度计',
        '19' => '冰排',
        '100' => '移动保温箱',
        '101' => '移动冷藏车',
        '102' => 'GSP冷藏车',
    ];

    public function getCoolerImageAttribute()
    {
        $image = '';
        switch ($this->cooler_type) {
            case self::设备类型_冷藏冰箱:
                $image = self::设备图片_冷藏冰箱_小型;
                if ($this->cooler_size > 900) {
                    $image = self::设备图片_冷藏冰箱_大型;
                }
                if ($this->cooler_size > 500) {
                    $image = self::设备图片_冷藏冰箱_中型;
                }
                break;
            case self::设备类型_冷冻冰箱:
                $image = self::设备图片_冷冻冰箱;
                break;
            case self::设备类型_普通冰箱:
                $image = self::设备图片_普通冰箱;
                break;
            case self::设备类型_冷藏冷库:
                $image = self::设备图片_冷藏冷库;
                break;
            case self::设备类型_冷冻冷库:
                $image = self::设备图片_冷冻冷库;
                break;
        }
        return $image;
    }

    public function cooler_info()
    {
        return $this->hasOne(CoolerInfo::class, 'cooler_id', 'cooler_id');
    }

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

    function cooler_category()
    {
        return $this->belongsTo(CoolerCategory::class, 'category_id', 'id');
    }
    //疫苗标签
    public function vaccine_tags()
    {
        return $this->belongsToMany(VaccineTags::class,'cooler_has_vaccine_tags','cooler_id','tag_id');
    }
    public function details()
    {
        return $this->belongsToMany(SysCoolerDetail::class,'cooler_details','cooler_id','sys_id')->withPivot('value');

    }
    public function coolerWarningTempLogs()
    {
        return $this->hasMany(CoolerWarningTempLogs::class,'cooler_id','cooler_id');
    }

    public function photos()
    {
        return $this->belongsToMany(SysCoolerPhoto::class,'cooler_photos','cooler_id','sys_id')->withPivot('value');

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
        return $value ? $value.'L' : '-';
    }

    public function getCoolerSize2Attr($value)
    {
        return $value ? $value.'L' : '-';
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
            ->whereRaw('((uninstall_time = 0 ) or uninstall_time >'.time_clock(0, date('Y-m-d', $month_start)).')and (install_time is NULL or install_time=0 or  install_time <'.time_clock(24, date('Y-m-d', $month_end)).')')
            ->orderBy('sort', 'desc')
            ->orderBy('category_id', 'asc')
            ->orderBy('cooler_id', 'desc');
    }

    public function statCooler()
    {

        return $this->hasMany(StatCooler::class, 'cooler_id', 'cooler_id');
    }

    public function getCoolerTypes()
    {
        $result = [];
        foreach (self::COOLER_TYPE as $key => $type) {
            $result['key'] = $key;
            $result['value'] = $type;
        }
        return $result;
    }

    static public function coolerType()
    {
        foreach (self::COOLER_TYPE as $key => $type) {
            $result[] = ['value' => 'type_'.$key, 'label' => $type];
        }
        return $result;
    }

    //各地设备数量统计
    public function getCountByType($company_ids, $filter)
    {
        $builder = $this->whereIn('company_id', $company_ids);
        if (isset($filter['status'])) {
            $builder = $builder->whereHas('cooler_info', function ($query) use ($filter) {
                $query->where('ice_state', $filter['status']);
            });
        }
        return $builder->selectRaw('
            ifnull(sum(if(cooler_type="7",1,0)),0) as type_7,
            ifnull(sum(if(cooler_type="14",1,0)),0)as type_14,
            ifnull(sum(if(cooler_type="5",1,0)),0) as type_5,
            ifnull(sum(if(cooler_type="6",1,0)),0) as type_6,
            ifnull(sum(if(cooler_type="3",1,0)),0) as type_3,
            ifnull(sum(if(cooler_type="13",1,0)),0)as type_13,
            ifnull(sum(if(cooler_type="4",1,0)),0) as type_4,
            ifnull(sum(if(cooler_type="1",1,0)),0) as type_1,
            ifnull(sum(if(cooler_type="16",1,0)),0)as type_16,
            ifnull(sum(if(cooler_type="17",1,0)),0)as type_17,
            ifnull(sum(if(cooler_type="12",1,0)),0)as type_12'
        )->first();
    }

    //各地设备容积统计
    public function getVolumeByStatus($company_ids, $filter)
    {
        $coolerInfoModel = new CoolerInfo();
        $prifix = $coolerInfoModel->getConnection()->getConfig('prefix');
        $coolerInfoTable = $prifix.$coolerInfoModel->getTable();
        $builder = $this->whereIn('company_id', $company_ids);
        if (isset($filter['cooler_type']) && $cooler_type = $filter['cooler_type']) {
            $builder = $builder->where('cooler_type', $cooler_type);
        }
        if (isset($filter['start_time']) && $start_time = $filter['start_time']) {
            $builder = $builder->whereRaw('left(cooler_starttime,4)>='.$start_time);
        }
        if (isset($filter['end_time']) && $end_time = $filter['end_time']) {
            $builder = $builder->whereRaw('left(cooler_starttime,4)<='.$end_time);
        }
        return $builder->join($coolerInfoModel->getTable(), function ($join) use ($coolerInfoModel) {
            $join->on($this->getTable().'.cooler_id', '=', $coolerInfoModel->getTable().'.cooler_id');
        })->selectRaw('
           count(1) as total_count,
           round(sum(cooler_size+cooler_size2)) as total_volume,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=1,1,0)),0) as total_count_status1,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=2,1,0)),0) as total_count_status2,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=3,1,0)),0) as total_count_status3,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=4,1,0)),0) as total_count_status4,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=5,1,0)),0) as total_count_status5,
            ifnull(round(sum((if('.$coolerInfoTable.'.ice_state=1,1,0)*(cooler_size+cooler_size2)))),0) as total_count_volume1,
            ifnull(round(sum((if('.$coolerInfoTable.'.ice_state=2,1,0)*(cooler_size+cooler_size2)))),0) as total_count_volume2,
            ifnull(round(sum((if('.$coolerInfoTable.'.ice_state=3,1,0)*(cooler_size+cooler_size2)))),0) as total_count_volume3,
            ifnull(round(sum((if('.$coolerInfoTable.'.ice_state=4,1,0)*(cooler_size+cooler_size2)))),0) as total_count_volume4,
            ifnull(round(sum((if('.$coolerInfoTable.'.ice_state=5,1,0)*(cooler_size+cooler_size2)))),0) as total_count_volume5
           '
        )->first();
    }

//冷链设备使用状态统计
    public function getCoolerStatus($company_ids, $filter)
    {
        $coolerInfoModel = new CoolerInfo();
        $prifix = $coolerInfoModel->getConnection()->getConfig('prefix');
        $builder = $this->whereIn('company_id', $company_ids);
        $coolerInfoTable = $prifix.$coolerInfoModel->getTable();
        if (isset($filter['start_time']) && $start_time = $filter['start_time']) {
            $builder = $builder->whereRaw('left(cooler_starttime,4)>='.$start_time);
        }
        if (isset($filter['end_time']) && $end_time = $filter['end_time']) {
            $builder = $builder->whereRaw('left(cooler_starttime,4)<='.$end_time);
        }
        return $builder->join($coolerInfoModel->getTable(), function ($join) use ($coolerInfoModel) {
            $join->on($this->getTable().'.cooler_id', '=', $coolerInfoModel->getTable().'.cooler_id');
        })->selectRaw('
           count(1) as total_count,
           round(sum((if(cooler_type=2,1,0)*(cooler_size+cooler_size2)))) as total_count_ld_volume,
           round(sum((if(cooler_type=1,1,0)*(cooler_size+cooler_size2)))) as total_count_lc_volume,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=1,1,0)),0) as total_count_status1,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=2,1,0)),0) as total_count_status2,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=3,1,0)),0) as total_count_status3,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=4,1,0)),0) as total_count_status4,
           ifnull(sum(if('.$coolerInfoTable.'.ice_state=5,1,0)),0) as total_count_status5,
           round(sum((if('.$coolerInfoTable.'.ice_state=1 and cooler_type=1,1,0)*(cooler_size+cooler_size2)))) as total_count_lc_volume1,
           round(sum((if('.$coolerInfoTable.'.ice_state=2 and cooler_type=1,1,0)*(cooler_size+cooler_size2)))) as total_count_lc_volume2,
           round(sum((if('.$coolerInfoTable.'.ice_state=3 and cooler_type=1,1,0)*(cooler_size+cooler_size2)))) as total_count_lc_volume3,
           round(sum((if('.$coolerInfoTable.'.ice_state=4 and cooler_type=1,1,0)*(cooler_size+cooler_size2)))) as total_count_lc_volume4,
           round(sum((if('.$coolerInfoTable.'.ice_state=5 and cooler_type=1,1,0)*(cooler_size+cooler_size2)))) as total_count_lc_volume5,  
           round(sum((if('.$coolerInfoTable.'.ice_state=1 and cooler_type=2,1,0)*(cooler_size+cooler_size2)))) as total_count_ld_volume1,
           round(sum((if('.$coolerInfoTable.'.ice_state=2 and cooler_type=2,1,0)*(cooler_size+cooler_size2)))) as total_count_ld_volume2,
           round(sum((if('.$coolerInfoTable.'.ice_state=3 and cooler_type=2,1,0)*(cooler_size+cooler_size2)))) as total_count_ld_volume3,
           round(sum((if('.$coolerInfoTable.'.ice_state=4 and cooler_type=2,1,0)*(cooler_size+cooler_size2)))) as total_count_ld_volume4,
           round(sum((if('.$coolerInfoTable.'.ice_state=5 and cooler_type=2,1,0)*(cooler_size+cooler_size2)))) as total_count_ld_volume5
           '
        )->first();
    }

    //新增冰箱
    public function addCooler($attributes)
    {
        return $this->create(array_only($attributes, $this->fillable));
    }

    //编辑冰箱
    public function editCooler($attributes)
    {
        return $this->update(array_only($attributes, $this->fillable));
    }

    //刷新探头数量

    public function flush_collector_num($cooler_id)
    {
        $cooler = Cooler::find($cooler_id);
        $map['cooler_id'] = $cooler_id;
        $map['status'] = 1;
        $count = Collector::where($map)->count();
        $cooler->update(['collector_num' => $count]);
    }

    //开关报警
    public function setWarningByStatus($status)
    {
        $count = 0;
        $message = '';
        $cooler = $this;
        if ($cooler['collector_num'] > 0) {
            $cooler->collectors()->update(['offline_check' => $status]);
            foreach ($cooler->collectorsOnline as $vo) {
                if ($vo->warningSetting) {
                    $vo->warningSetting()->update(['temp_warning' => $status]);
                } else {
                    $count++;
                    $message = $count.'个探头未设置报警';
                }
            }
        } else {
            $count = -1;
            $message = '未绑定探头';
        }
        return ['count' => $count, 'message' => $message];

    }
    //冷库数量
    public function getCoolerCountByCoolerType($company_id,$cooler_type,$status='')
    {
        if ($status)
        return $this->where('status',$status)->whereIn('company_id',$company_id)->whereIn('cooler_type', $cooler_type)->count();
        else
            return $this->where('status','<>',4)->whereIn('company_id',$company_id)->whereIn('cooler_type', $cooler_type)->count();

    }

    public function sysDetails()
    {
        return $this->hasManyThrough(\App\Models\Ccrp\Sys\SysCoolerDetail::class,'cooler_detail','cooler_id','sy_cooler_detail_id','id');
    }


    public function saveDetails($details = [])
    {
        if(count($details)>0)
        {
            $sysColumns = \App\Models\Ccrp\Sys\SysCoolerDetail::columns();
            $cooler_id = $this->cooler_id;
            $sysColumnsId = \App\Models\Ccrp\Sys\SysCoolerDetail::columns('id','slug');
//            dd($details);
            $data = array_intersect_key($details,$sysColumns);
            $new_data = [];
            foreach($data as $key => $datum)
            {
                if($datum and strlen($datum)>0)
                {
//                    $row = [
//                        'cooler_id'=>$this->cooler_id,
//                        'sys_id'=>$sysColumnsId[$key],
//                        'value'=>$datum,
//                    ];
                    $detail = CoolerDetail::firstOrCreate(['cooler_id'=>$this->cooler_id,
                        'sys_id'=>$sysColumnsId[$key]]);
                    $detail->value = $datum;
                    $new_data[] = $detail;
                }
            }
            $rs = $this->details()->saveMany($new_data);
        }
    }


    public function getStatusAttr($value)
    {
        return self::$status[$value];
    }

    public function getCoolerTypeAttr($value)
    {
        return self::$cooler_type[$value];
    }


    /**
     * 联动下拉框数据
     * @return array
     */
    public static function lists_status()
    {
        return [
            'list' => array2list(self::$status),
            'default' => array2keys(self::$status) //所有状态
        ];

    }


    public function validate()
    {
        return $this->hasOne(CoolerValidate::class, 'cooler_id', 'cooler_id');
    }

//巡检报表-冷库类型
    public function getCoolerByType($company_id, $quarter)
    {
        $date = dateFormatByType($quarter);
        $start=Carbon::createFromTimestamp($date['end'])->startOfDay()->timestamp;
        $company_ids = Company::find($company_id)->ids(0);
        $coolers = $this->selectRaw('
        sum(IF(cooler_type="5" or cooler_type="6",1,0)) as lk_count,
        sum(IF(cooler_type!="5" and cooler_type!="6" and cooler_type!="101",1,0)) as bx_count,
        sum(IF(cooler_type="101",1,0)) as lcc_count')
            ->whereRaw('((uninstall_time = 0 ) or uninstall_time >' . $start . ') and (install_time is NULL or install_time=0 or  install_time <' . $date['end'] . ')')
            ->whereIn('company_id', $company_ids)
            ->first()
            ->toArray();
        return $coolers;
    }

    //巡检报表-冷链装备信息不规范清单
    public function getUnCompleteCooler($company_id, $quarter = '')
    {
        $company_ids = Company::find($company_id)->ids(0);
        return $this
            ->whereRaw("status!=4 and (length(cooler_sn)=0 or length(cooler_brand)=0 or length(cooler_model)=0 or (length(cooler_size)=0  and length(cooler_size2)=0) or length(cooler_starttime)=0 or is_medical=0)")
            ->whereIn('company_id', $company_ids)
            ->with(['company' => function ($query) {
                $query->select('id', 'title');
            }])->get()->toArray();
    }
//巡检报表-冷链装备状态清单
    public function getUselessCooler($company_id, $quarter = '')
    {
        $date = dateFormatByType($quarter);
        $company_ids = Company::find($company_id)->ids(0);
        return $this->selectRaw('company_id,cooler_name,cooler_sn,collector_num,status,uninstall_time')
            ->whereRaw('status!=1 and (uninstall_time between ' . $date['start'] . ' and ' . $date['end'] . ')')
            ->whereIn('company_id', $company_ids)
            ->with(['company' => function ($query) {
                $query->select('id', 'title');
            }])->get()->toArray();

    }

    //备用、维修、启用 关闭探头，关闭报警
    public function ChangeCoolerStatus($cooler,$status,$note,$note_uid=0)
    {
            $post['cooler_id'] = $cooler['cooler_id'];
            $post['cooler_sn'] = $cooler['cooler_sn'];
            $post['cooler_name'] = $cooler['cooler_name'];
            $post['category_id'] = $cooler['category_id'];
            $post['company_id'] = $cooler['company_id'];
            $post['status'] = $status;
            $post['note'] =$note;
            $post['note_time'] = time();
            $post['note_uid'] = $note_uid;
            //添加操作日志
            $logmodel = CoolerLog::create($post);
            $set['status'] = $status;
            $warning_set = $status == 1 ? 1 : 0;
            if ($logmodel) {
                if ($status == Cooler::状态_报废) {
                    $set['uninstall_time'] = time();
                    $cooler->update($set);
                    if ($cooler['collector_num'] > 0) {
                        foreach ($cooler->collectors as $vo) {
                            (new Collector)->uninstall($vo['collector_id'], '冷链装备报废');
                        }
                    }
                } else {
                    $cooler->update($set);
                    $cooler->setWarningByStatus($warning_set);
                }
                return $cooler;
        }
    }
}
