<?php

namespace App\Models\Ccrp;

use function App\Utils\abs2;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GatewaybindingdataModel
 * 分类模型，关系库
 * 删除记录bingdingtype=1， status= 0 、
 * 新增、修改记录bingdingtype=0, status=0，2个情况。
 * status=0m优先级最高代表是否跟下位机交互=1不交互。
 * 其次是bingdingtype代表交互的内容
 */
class GatewaybindingdataModel extends Model
{
    protected $table = 'gatewaybindingdata';
    protected $connection = 'DB_LX01CMD'; //DB_CAIJI
    public $collector = null;
    public $ledspeaker = null;
    public $sensor_model = null;
    public $collector_name = null;
    public $ledspeaker_model = null;
    public $GatawayMac = null;
    public $SensorMac = null;
    const     SENSOR_MODEL = 'LWTG310S';
    const     SENSOR_MODEL_OLD = 'LWTG310';
    const     SENSOR_MODEL_D = 'LWTGD310S';
    const     LEDSPEAKER_MODEL = 'LWZST300S';

    public $timestamps = false;

    /**
     * @return bool
     */
    public function do_ledspeaker_delete()
    {
        $setting['BindingType'] = 1;
        return $this->_update_ledspeaker($setting);
    }

    /**
     * @param $temp_high
     * @param $temp_low
     * @return bool
     */
    public function do_warning_setting_open($temp_high, $temp_low)
    {
        $setting['TemperatrueHigh'] = $temp_high;
        $setting['TemperatureLow'] = $temp_low;
        return $this->do_mod_collector($setting);
    }

    /**
     * @return bool
     */
    public function do_warning_setting_close()
    {
        $setting['TemperatrueHigh'] = 200;
        $setting['TemperatureLow'] = -200;
        return $this->do_mod_collector($setting);
    }


    /**
     * @param $setting
     * @return bool|mixed
     */
    public function do_add($setting = array())
    {
        if (!isset($setting['BindingType'])) $setting['BindingType'] = 0;
        if (!isset($setting['status'])) $setting['status'] = 0;
        if (!isset($setting['TemperatrueHigh'])) $setting['TemperatrueHigh'] = 200;
        if (!isset($setting['TemperatureLow'])) $setting['TemperatureLow'] = -200;
        if (!isset($setting['HumidityHigh'])) $setting['HumidityHigh'] = 100;
        if (!isset($setting['HumidityLow'])) $setting['HumidityLow'] = 0;
        if (!isset($setting['DisplayName'])) $setting['DisplayName'] = $this->collector_name;
        if ($find = $this->_find()) {
            return $this->_update($setting);
        } else {

            return $this->_insert($setting);
        }
    }

    /**
     * @param $setting
     * @return bool
     * 新增、修改记录BindingType=0, status=0
     */
    public function do_mod($setting)
    {
        $setting['BindingType'] = 0;
        $setting['status'] = 0;
        if ($this->_find()) {
            return $this->_update($setting);
        }
        return false;
    }

    /**
     * @param $setting
     * @return bool
     * 新增、修改记录BindingType=0, status=0
     */
    public function do_mod_collector($setting = array())
    {
        $setting['status'] = 0;
        if ($this->_find_collector()) {
            return $this->_update_collector($setting);
        }
        return false;
    }

    /**
     * @return bool
     * 删除记录BindingType=1， status= 0
     */
    public function do_del_ledspeaker()
    {
        $setting['BindingType'] = 1;
        $setting['status'] = 0;
        return $this->_update_ledspeaker($setting);
    }

    /**
     * @return bool
     * 删除记录BindingType=1， status= 0
     */
    public function do_del_collector()
    {
        $setting['status'] = 0;
        $setting['BindingType'] = 1;
        return $this->_update_collector($setting);
    }

    /**
     * @return bool
     * 删除记录BindingType=1， status= 0
     */
    public function do_del()
    {
        $setting['BindingType'] = 1;
        $setting['status'] = 0;
        if ($this->_find()) {
            return $this->_update($setting);
        }
        return false;
    }

    /**
     * @return bool
     */
    private function _find()
    {
        if ($this->check_collector() and $this->check_ledspeaker()) {
            $where['GatewayMac'] = $this->GatawayMac;
            $where['SensorMac'] = $this->SensorMac;
            if ($this->where($where)->find())
                return true;
            else
                return false;
        } else return false;
    }

    /**
     * @return bool
     */
    private function _find_collector()
    {
        if ($this->check_collector()) {
            $where['SensorMac'] = $this->SensorMac;
            $where['BindingType'] = 0;
            if ($this->where($where)->first())
                return true;
            else
                return false;
        } else return false;
    }

    /**
     * @param $setting
     * @return bool
     */
    private function _update($setting)
    {
        if ($this->check_collector() and $this->check_ledspeaker()) {
            $setting['BindingType'] = 0;
            $setting['status'] = 0;
            $where['GatewayMac'] = $this->GatawayMac;
            $where['SensorMac'] = $this->SensorMac;
            return $this->where($where)->update($setting);
        }
        return false;
    }

    /**
     * @param $setting
     * @return bool
     */
    private function _update_collector($setting)
    {
        if ($this->check_collector()) {
            $setting['status'] = 1;
            $where['SensorMac'] = $this->SensorMac;
            $rs = $this->where($where)->update($setting);
            return $rs;
        }
        return false;
    }

    /**
     * @param $setting
     * @return bool
     */
    private function _update_ledspeaker($setting)
    {
        if ($this->check_ledspeaker()) {
            if (!isset($setting['BindingType'])) $setting['BindingType'] = 0;
            if (!isset($setting['status'])) $setting['status'] = 0;
            $where['GatewayMac'] = $this->GatawayMac;
            return $this->where($where)->update($setting);
        }
        return false;

    }

    /**
     * @param $setting
     * @return bool|mixed
     */
    private function _insert($setting)
    {

        if ($this->check_collector() and $this->check_ledspeaker()) {
            $setting['GatewayMac'] = $this->GatawayMac;
            $setting['SensorMac'] = $this->SensorMac;
            return $this->create($setting);
        }
        return false;
    }

    /**
     * @return bool
     */
    private function check_collector()
    {
        if ($this->collector and ($this->sensor_model == self::SENSOR_MODEL_OLD or $this->sensor_model == self::SENSOR_MODEL or $this->sensor_model == self::SENSOR_MODEL_D)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function check_ledspeaker()
    {
        if ($this->ledspeaker and $this->ledspeaker_model == self::LEDSPEAKER_MODEL) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     */
    public function set_collector($id)
    {
        $this->collector = Collector::find($id);
        $this->sensor_model = $this->collector['supplier_product_model'];
        $this->SensorMac = $this->format_mac(abs2($this->collector['supplier_collector_id']));
        $this->collector_name = $this->collector['collector_name'];
    }

    /**
     * @param $id
     */
    public function set_ledspeaker($id)
    {
        $this->ledspeaker = Ledspeaker::find($id);
        $this->ledspeaker_model = $this->ledspeaker['supplier_model'];
        $this->GatawayMac = $this->format_mac(abs2($this->ledspeaker['supplier_ledspeaker_id']));
    }

    /**
     * @param $mac
     * @return string
     */
    public function format_mac($mac)
    {
        $mac = str_replace(' ', '', $mac);
        $mac_str = '';
        // 11184720  to: 11 18 47 20
        $i = 0;
        while (isset($mac{$i})) {
            $mac_str .= $mac{$i};
            if ($i == 1 or $i == 3 or $i == 5) {
                $mac_str .= ' ';
            }
            $i++;
        }
        return $mac_str;
    }

    /**
     * @param $gateway_mac
     * @return bool
     */
    function clear_binding_gateway($gateway_mac)
    {
        $gateway_mac_str = $this->format_mac($gateway_mac);
        $this->where(array('GatewayMac' => $gateway_mac_str))->update(['status' => 0]);
        return $this->where(array('GatewayMac' => $gateway_mac_str))->update(['BindingType' => 1]);
        //1代表解除下发设定。0代表建立、更新下发。
    }

    /**
     * @param $sensor_mac
     * @return bool
     */
    function clear_binding_sensor($sensor_mac)
    {
        $sensor_mac_str = $this->format_mac($sensor_mac);
        $this->where(array('SensorMac' => $sensor_mac_str))->update(['status' => 0]);
        return $this->where(array('SensorMac' => $sensor_mac_str))->update(['BindingType' => 1]); //1代表解除下发设定。0代表建立、更新下发。
    }

    /**
     * @param $gateway_mac
     * @param $sensor_mac
     * @param int $setting 1代表解除下发设定。0代表建立、更新下发。
     * @return bool
     */
    function reset_binding($gateway_mac, $sensor_mac, $setting = 1)
    {
        $sensor_mac_str = $this->format_mac($sensor_mac);
        $gateway_mac_str = $this->format_mac($gateway_mac);
        $this->where(array(
            'SensorMac' => $sensor_mac_str,
            'GatewayMac' => $gateway_mac_str
        ))->update(['status' => 0]);
        return $this->where(array(
            'SensorMac' => $sensor_mac_str,
            'GatewayMac' => $gateway_mac_str
        ))->update(['BindingType' => $setting]);
    }

    /**
     * @param $gateway_mac
     * @param $sensor_mac
     * @param $sensor_name
     * @return mixed
     */
    function add_binding($gateway_mac, $sensor_mac, $sensor_name)
    {
        $gateway_mac_str = $this->format_mac($gateway_mac);
        $sensor_mac_str = $this->format_mac($sensor_mac);
        $data['SensorMac'] = $sensor_mac_str;
        $data['GatewayMac'] = $gateway_mac_str;
        $have = $this->where($data)->count();
        if ($have) {
            $this->reset_binding($gateway_mac_str, $sensor_mac_str, 0);
        } else {
            $data['TemperatrueHigh'] = 200;
            $data['TemperatureLow'] = -200;
            $data['HumidityHigh'] = 100;
            $data['HumidityLow'] = 0;
            $data['DisplayName'] = $sensor_name;
            $data['BindingType'] = 0;
            $data['Status'] = 0;    //0代表需要下发的，处理完会自动变成1
            return $this->create($data);
        }
    }

    function set_binding_sensor($sensor_mac, $setting = array())
    {
        $sensor_mac_str = $this->format_mac($sensor_mac);
        $setting['status'] = 0; //0代表需要下发的，处理完会自动变成1
        return $this->where(array('SensorMac' => $sensor_mac_str))->update($setting);
    }

    //更新报警
    public function refresh_warning_setting_by_collector($collector_id)
    {
        $collector = Collector::select('supplier_product_model', 'supplier_collector_id')->where(array('collector_id' => $collector_id))->first();
        if ($collector['supplier_product_model'] == 'LWTG310S' or $collector['supplier_product_model'] == 'LWTGD310S') {
            $warning_setting = WarningSetting::where(array('collector_id' => $collector_id))->first();
            $setting['TemperatrueHigh'] = 200;
            $setting['TemperatureLow'] = -200;
            if ($warning_setting->temp_warning == 1 and $warning_setting->status == 1) {
                $setting['TemperatrueHigh'] = $warning_setting->temp_high;
                $setting['TemperatureLow'] = $warning_setting->temp_low;
            }
            return $this->set_binding_sensor($collector['supplier_collector_id'], $setting);
        }
        return false;

    }
}