<?php

namespace App\Models\Ccrp;

class SenderWarningSetting extends Coldchain2Model
{
    protected $table = 'sender_warning_setting';
    public $timestamps = false;
    protected $fillable = [
        'sender_id',
        'power_warning',
        'power_warning_last',
        'power_warning2_last',
        'power_warning3_last',
        'set_time',
        'set_uid',
        'warninger_id',
        'warninger2_id',
        'warninger3_id',
        'category_id',
        'company_id',
        'status',
    ];

    const 预警预警关闭 = 0;
    const 预警预警开启 = 1;


    const POWER_WARNING = [
        self::预警预警关闭 => '关闭',
        self::预警预警开启 => '开启',
    ];
    const STATUS = [
        0 => '关闭',
        1 => '开启',
    ];

    function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    public function sender()
    {
        return $this->belongsTo(Sender::class, 'sender_id', 'sender_id');
    }

    public function warninger()
    {
        return $this->belongsTo(Warninger::class, 'warninger_id', 'warninger_id');
    }


    public function create(array $attributes = [], array $options = [])
    {
        $result = parent::create($attributes);
        if ($result) {
            $self = $this->find($result->id);
            $collector = Collector::find($self['collector_id']);
            if ($collector['supplier_product_model'] == 'LWTG310S' or $collector['supplier_product_model'] == 'LWTGD310S') {
                $gateway = new GatewaybindingdataModel();
                $gateway->set_collector($self['collector_id']);
                if ($self['temp_warning'] == 1 and $self['status'] == 1) {
                    $gateway->do_warning_setting_open($self['temp_high'], $self['temp_low']);
                } else {
                    $gateway->do_warning_setting_close();
                }
            }

        }
        return $result;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $save = parent::update($attributes, $options);;
        $self = $this->find($this->id);
        $collector = Collector::find($self['collector_id']);
        if ($collector['supplier_product_model'] == 'LWTG310S' or $collector['supplier_product_model'] == 'LWTGD310S') {
            $gateway = new GatewaybindingdataModel();
            $gateway->set_collector($self['collector_id']);
            if ($self['temp_warning'] == 1 and $self['status'] == 1) {
                $gateway->do_warning_setting_open($self['temp_high'], $self['temp_low']);
            } else {
                $gateway->do_warning_setting_close();
            }
        }
        return $save;
    }
}
