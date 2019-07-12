<?php

namespace App\Models\Ccrp;


/**
 * Class Collector
 * @package App\Models
 */
class Ledspeaker extends Coldchain2Model
{
    protected $table = 'ledspeaker';
    protected $primaryKey = 'ledspeaker_id';
    protected $fillable = [
        'ledspeaker_name',
        'supplier_id',
        'supplier_ledspeaker_id',
        'supplier_model',
        'simcard',
        'category_id',
        'company_id',
        'update_time',
        'install_time',
        'uninstall_time',
        'refresh_time',
        'install_uid',
        'collector_num',
        'collector_id',
        'sender_id',
        'module',
        'status',
        'sort',
    ];

    const  LEDSPEAKER_MODULE = [
        '0' => '报警模式（不显示）',
        '1' => '实时播报（显示+报警）',
        '2' => '实时显示（不报警）'
    ];

    public function collectors()
    {
        return Collector::whereIn('collector_id', explode(',', $this->collector_id))->select('collector_id','collector_name','cooler_name')->get() ?? [];
    }

    public function senders()
    {
        return Sender::whereIn('id', explode(',', $this->sender_id))->select('sender_id','note')->get() ??[];
    }

    public function get_products()
    {
        $products = Product::where('status',1)->whereIn('product_type',[1, 2])->orderBy('sort','desc')->get();
        $data = array();
        foreach ($products as $item) {
            $data[]=['value'=>$item['supplier_product_model'],'title'=>($item['product_type'] == 2 ? '一体机: ' : '报警器: ') . $item['product_ model']];
        }
        return $data;

    }
    public function create($attribute)
    {
        $result=parent::create($attribute);
        if ($result) {
            if ($result['supplier_model'] == 'LWZST300S') {
                $collector_id = $result['collector_id'];
                $gateway = new GatewaybindingdataModel();
                $gateway->set_ledspeaker($result['ledspeaker_id']);
                $gateway->do_del_ledspeaker();
                if ($collector_id) {
                    $collector_ids = explode(',', $collector_id);
                    if ($result['status'] == 1)   //status==2，报废，解除不添加
                        foreach ($collector_ids as $vo) {
                            $gateway->set_collector($vo);
                            $gateway->do_add();
                        }
                }
            }
        }
        return $result;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $result= parent::update($attributes, $options);
        $self = $this->find($this->ledspeaker_id);
        if ($self['supplier_model'] == 'LWZST300S') {
            $collector_id = $self['collector_id'];
            $gateway = new GatewaybindingdataModel();
            $gateway->set_ledspeaker($self['ledspeaker_id']);
            $gateway->do_del_ledspeaker();
            if ($collector_id) {
                $collector_ids = explode(',', $collector_id);
                if ($self['status'] == 1)   //status==2，报废，解除不添加
                {
                    foreach ($collector_ids as $vo) {
                        $gateway->set_collector($vo);
                        $warning_setting = new WarningSetting();
                        $renew_warning_setting = array();
                        $warning_setting = $warning_setting->where(array('collector_id' => $vo, 'status' => 1, 'temp_warning' => 1))->first();
                        if ($warning_setting) {
                            $renew_warning_setting['status'] = 0;
                            $renew_warning_setting['TemperatrueHigh'] = $warning_setting['temp_high'];
                            $renew_warning_setting['TemperatureLow'] =$warning_setting['temp_low'];
                        }
                        $gateway->do_add($renew_warning_setting);
                    }
                }
            }
        } elseif ($self['supplier_model'] == 'LDH500') {
            $update['isbind_sender'] = 0;
            $update['bind_sender_id'] = '';
            $update['isbind_qingxi_sender'] = 0;
            Collector::where(array('bind_sender_id' => $self['supplier_ledspeaker_id']))->update($update);
            $collector_id = $self['collector_id'];
            if ($collector_id) {
                $collector_ids = explode(',', $collector_id);
                if ($self['status'] == 1)   //status==2，报废，解除不添加
                {
                    foreach ($collector_ids as $vo) {
                        $update['isbind_sender'] = 1;
                        $update['isbind_qingxi_sender'] = 1;
                        $update['bind_sender_id'] = trim($self['supplier_ledspeaker_id']);
                       Collector::where(array('collector_id' => $vo))->update($update);
                    }
                }
            }
        }elseif($self['supplier_ledspeaker_id']){
            $update['isbind_sender'] = 0;
            $update['bind_sender_id'] = '';
            $update['isbind_qingxi_sender'] = 0;
           Collector::where(array('bind_sender_id' => $self['supplier_ledspeaker_id']))->update($update);
        }
        return $result;
    }

    public function getLedspeaker_module()
    {
        $result=[];
        foreach (self::LEDSPEAKER_MODULE as $k=> $value)
        {
            $result[]=[
                'value'=>$k,
                'label'=>$value
            ];
        }
        return $result;
    }

    public function setCollectorIdAttribute($value)
    {
            $this->attributes['collector_id']=is_array($value)?implode(',',$value):$value;
    }
    public function setSenderIdAttribute($value)
    {
        $this->attributes['sender_id']=is_array($value)?implode(',',$value):$value;
    }
}
