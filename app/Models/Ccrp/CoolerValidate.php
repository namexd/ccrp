<?php

namespace App\Models\Ccrp;

class CoolerValidate extends Coldchain2Model
{
    protected $table = 'cooler_validates';
    protected $fillable = [
        'company_id',
        'company_title',
        'cooler_id',
        'cooler_sn',
        'cooler_name',
        'cooler_cdc_sn',
        'cooler_type',
        'come_from',
        'comporation',
        'cooler_brand',
        'cooler_model',
        'product_sn',
        'cooler_size',
        'cooler_size2',
        'product_date',
        'arrive_date',
        'cooler_starttime',
        'is_medical',
        'medical_permission',
        'has_double_power',
        'has_power_generator',
        'has_double_compressor',
        'cooler_status',
        'validate_name',
        'validate_time',
        'validate_time',
        'validate_status',
        'creatd_at',
        'updated_at'
    ];

    const COOLER_STATUS = [
        '正常' => '正常',
        '待修' => '待修',
        '报废' => '报废',
        '备用' => '备用',
        '迁出' => '迁出',
    ];
    const 普通冷库 = '普通冷库';
    const 低温冷库 = '低温冷库';
    const 普通冰箱 = '普通冰箱';
    const 低温冰箱 = '低温冰箱';
    const 台式小冰箱 = '台式小冰箱';
    const COOLER_TYPE = [
        self::普通冷库 => self::普通冷库,
        self::低温冷库 => self::低温冷库,
        self::普通冰箱 => self::普通冰箱,
        self::低温冰箱 => self::低温冰箱,
        self::台式小冰箱 => self::台式小冰箱,
    ];
    const 本级自购 = '本级自购';
    const 上级下发 = '上级下发';
    const 设备迁入 = '设备迁入';
    const 捐赠 = '捐赠';
    const COME_FROM = [
        self::本级自购 => self::本级自购,
        self::上级下发 => self::上级下发,
        self::设备迁入 => self::设备迁入,
        self::捐赠 => self::捐赠,
    ];
    const IS_MEDICAL = ['否'=>'否','是'=>'是'];

    public function cooler()
    {
        return $this->belongsTo(Cooler::class, 'cooler_id', 'cooler_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}
