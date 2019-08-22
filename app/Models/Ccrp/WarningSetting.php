<?php

namespace App\Models\Ccrp;

class WarningSetting extends Coldchain2Model
{
    protected $table = 'warning_setting';
    public $timestamps = false;
    protected $fillable = [
        'collector_id',
        'warning_strategy_id',
        'temp_warning',
        'humi_warning',
        'volt_warning',
        'temp_high',
        'temp_low',
        'humi_high',
        'humi_low',
        'volt_high',
        'volt_low',
        'temp_warning_last',
        'temp_warning2_last',
        'temp_warning3_last',
        'humi_warning_last',
        'humi_warning2_last',
        'humi_warning3_last',
        'volt_warning_last',
        'set_time',
        'set_uid',
        'warninger_id',
        'warninger2_id',
        'warninger3_id',
        'category_id',
        'company_id',
        'status',
        'note',
    ];

    const 温度预警关闭 = 0;
    const 温度预警开启 = 1;
    const TEMP_WARNING = [
        self::温度预警关闭 => '关闭',
        self::温度预警开启 => '开启',
    ];
    const 预警关闭 = 0;
    const 预警开启 = 1;
    const STATUS = [
        self::预警关闭 => '关闭',
        self::预警开启 => '开启',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class, 'collector_id', 'collector_id');
    }

    public function warninger()
    {
        return $this->belongsTo(Warninger::class, 'warninger_id', 'warninger_id');
    }

    function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
   const WARNING_TIME = [
        'WARNING_TIME_LAST' => array(
            '1' => 30,
            '2' => 60,
            '3' => 60,
        ),
        'POWER_WARNING_TIME_LAST' => array(
            '1' => 0,
            '2' => 60,
            '3' => 60,
        ),
        'DELIVERORDER_WARNING_TIME_LAST' => array(
            '1' => 5,
            '2' => 10,
            '3' => 15,
        ),
    ];

    public function create(array $attributes = [], array $options = [])
    {
        $result=parent::create($attributes);
        if($result){
            $self = $this->find($result->id);
            $collector =Collector::find($self['collector_id']);
            if($collector['supplier_product_model']=='LWTG310S' or  $collector['supplier_product_model']=='LWTGD310S'){
                $gateway = new GatewaybindingdataModel();
                $gateway->set_collector($self['collector_id']);
                if($self['temp_warning']==1 and $self['status']==1){
                    $gateway->do_warning_setting_open($self['temp_high'],$self['temp_low']);
                }else{
                    $gateway->do_warning_setting_close();
                }
            }

        }
        return $result;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $save =  parent::update($attributes,$options);;
        $self = $this->find($this->id);
        $collector = Collector::find($self['collector_id']);
        if($collector['supplier_product_model']=='LWTG310S' or $collector['supplier_product_model']=='LWTGD310S'){
            $gateway = new GatewaybindingdataModel();
            $gateway->set_collector($self['collector_id']);
            if($self['temp_warning']==1 and $self['status']==1){
                $gateway->do_warning_setting_open($self['temp_high'],$self['temp_low']);
            }else{
                $gateway->do_warning_setting_close();
            }
        }
        return   $save;
    }
//巡检报告-预警信息不完整清单

    public function getUnCompleteWarnings($company_id, $quarter = '')
    {
        $company_ids = Company::find($company_id)->ids(0);
        return $this->where(function ($query){
            $query->whereDoesntHave('warninger')->orWhere(function ($query) {
                $query->whereHas('company', function ($query) {
                    $query->whereRaw('offline_send_type=99 and offline_send_warninger_id=0');
                });
            });
        })->whereIn('company_id', $company_ids)
            ->where('status',1)
            ->with(['warninger'=>function($query){
                $query->selectRaw('warninger_id,warninger_type');
            },'company'=>function($query){
                $query->selectRaw('id,offline_send_type,title,manager,offline_send_warninger_id');
            },'collector'=>function($query){
                $query->selectRaw('cooler_id,collector_id,supplier_collector_id');
            },'collector.cooler'=>function($query){
                $query->selectRaw('cooler_id,cooler_sn');
            }])
            ->selectRaw('company_id,warninger_id,collector_id')
            ->get()
            ->toArray();

//            ->toSql();
    }
}
