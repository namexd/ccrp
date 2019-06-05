<?php

namespace App\Models\Ccrp;

use App\Traits\ModelFields;
use Carbon\Carbon;
use GuzzleHttp\Client;
use SoapClient;

class Vehicle extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'vehicle';
    protected $primaryKey = 'vehicle_id';
    protected $fillable = ['vehicle_id',
        'vehicle',
        'gps_time',
        'address',
        'refresh_time',
        'install_time'];
    const VECHICLE_CONFIG = [
        'VEHICLE_WEBSERIVES' => 'http://www.chinacoldchain.com:8178/WebService.asmx?wsdl',
        'VEHICLE_WEBSERIVES_LW' => 'http://www.chinacoldchain.com:9998/ServiceLw.asmx?wsdl', //chinacoldchain车载用户名
        'VEHICLE_USERNAME' => 'shlw', //chinacoldchain车载用户名
        'VEHICLE_PASSWORD' => '098765',//chinacoldchain车载密码
        'BAIDU_MAP_API_KEY' => 'GzGStHdTgf1Z774pqvWPITMR',//浏览器端KEY
        'BAIDU_MAP_API_KEY_SERVER' => 'C2721ec9a71667919ac7eb857e24cfeb',//服务器端KEY
        'VEHICLE_GETPOSITION' => 'http://www.chinacoldchain.com:8178/interface/MapServices/GetPosition.aspx',//获取位置
        'VEHICLE_ORIENTATION' => 'http://www.chinacoldchain.com:8178/interface/MapServices/Orientation.aspx',//当前位置
        'VEHICLE_PlAYTRACk' => 'http://www.chinacoldchain.com:8178/interface/MapServices/PlayTrack.aspx',//历史轨迹
        'VEHICLE_LOGINNAME' => array(
            'zhll' => '123456',
            'shlw' => '098765',
            'shmc' => '123456',
        ),

    ];

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->field('id,title,short_title');
    }

    public function refresh_address()
    {
        $vehicle = $this;
        try {
            $soap = new SoapClient(self::VECHICLE_CONFIG['VEHICLE_WEBSERIVES']);
            $user = $this->get_vehicle_loginname($vehicle['vehicle']);
            $array = array(
                'p_regname' => $vehicle['vehicle'],
                'p_userCode' => $user['username'],
                'p_userPsw' => $user['password'],
            );
            $result = $soap->GetVehicleLocation($array);
            $rs = $result->GetVehicleLocationResult;
            $rs_obj = json_decode($rs);
            $data = $rs_obj->{$vehicle['vehicle']};
            $data2['lon'] = $data->Lon;
            $data2['lat'] = $data->Lat;
            $data2['gps_time'] = $data->GpsTime;
            $data2['refresh_time'] = time();
            ///获取地址信息：
            $address = $this->get_vehicle_address($data2['lon'], $data2['lat']);
            $data2['address'] = $address;
            array_forget($data2, ['lon', 'lat']);
            $this->fill($data2);
            $this->save();
            return $this;
        } catch (\SoapFault $e) {
            return $e->getMessage();
        }

    }

    public function vehicle_temp($filter,$start,$end)
    {
        if (array_has($filter, 'vehicle') or array_has($filter, 'id')) {
            if (array_has($filter, 'vehicle')) {
                $vehicle = array_get($filter, 'vehicle');
            } else {
                $id = array_get($filter, 'id');
                $vehicle_obj = $this->where('vehicle_id', $id)->first();
                $vehicle = $vehicle_obj['vehicle'];
            }

            $user = $this->get_vehicle_loginname($vehicle);
                $soap = new SoapClient(self::VECHICLE_CONFIG['VEHICLE_WEBSERIVES']);

                $str =['str' => '<Root><UserCode>'.$user['username'].'</UserCode><UserPsw>'.$user['password'].'</UserPsw><Data><key>1</key><RegName>'.$vehicle.'</RegName><BaginTime>'.$start.'</BaginTime><EndTime>'.$end.'</EndTime></Data></Root>'];
                $result = $soap->GetTemperatureList($str);    //GetTpProbe()  //GetTemperatureList
                $rs = $result->GetTemperatureListResult;
                $arr = json_decode(json_encode((array)simplexml_load_string($rs)), true);
                if (array_has( $arr['Data'],'Temperature')&&$data_arr = $arr['Data']['Temperature'])
                {
                    $vehicle_obj = $this->where('vehicle', $vehicle)->first();
                    if ($vehicle_obj['temp_fix']) {
                        foreach ($data_arr as &$vo) {
                            if ($vo['Temperature'] > -99)
                                $vo['Temperature'] += $vehicle_obj['temp_fix1'];
                            if ($vo['Temperature2'] > -99)
                                $vo['Temperature2'] += $vehicle_obj['temp_fix2'];
                            if ($vo['Temperature3'] > -99)
                                $vo['Temperature3'] += $vehicle_obj['temp_fix3'];
                            if ($vo['Temperature4'] > -99)
                                $vo['Temperature4'] += $vehicle_obj['temp_fix4'];
                            //temp limit
                            if ($vehicle_obj['temp_limit']) {
                                if (($vo['Temperature'] > $vehicle_obj['temp_limit_high']) and ($vo['Temperature'] < ($vehicle_obj['temp_limit_high'] + $vehicle_obj['temp_limit_offset']))) {
                                    $vo['Temperature'] = $vo['Temperature'] - $vehicle_obj['temp_limit_offset'];
                                } elseif (($vo['Temperature'] < $vehicle_obj['temp_limit_low']) and ($vo['Temperature'] < ($vehicle_obj['temp_limit_low'] - $vehicle_obj['temp_limit_offset']))) {
                                    $vo['Temperature'] = $vo['Temperature'] + $vehicle_obj['temp_limit_offset'];
                                }


                                if (($vo['Temperature2'] > $vehicle_obj['temp_limit_high']) and ($vo['Temperature2'] < ($vehicle_obj['temp_limit_high'] + $vehicle_obj['temp_limit_offset']))) {
                                    $vo['Temperature2'] = $vo['Temperature2'] - $vehicle_obj['temp_limit_offset'];
                                } elseif (($vo['Temperature2'] < $vehicle_obj['temp_limit_low']) and ($vo['Temperature2'] < ($vehicle_obj['temp_limit_low'] - $vehicle_obj['temp_limit_offset']))) {
                                    $vo['Temperature2'] = $vo['Temperature2'] + $vehicle_obj['temp_limit_offset'];
                                }


                                if (($vo['Temperature3'] > $vehicle_obj['temp_limit_high']) and ($vo['Temperature3'] < ($vehicle_obj['temp_limit_high'] + $vehicle_obj['temp_limit_offset']))) {
                                    $vo['Temperature3'] = $vo['Temperature3'] - $vehicle_obj['temp_limit_offset'];
                                } elseif (($vo['Temperature3'] < $vehicle_obj['temp_limit_low']) and ($vo['Temperature3'] < ($vehicle_obj['temp_limit_low'] - $vehicle_obj['temp_limit_offset']))) {
                                    $vo['Temperature3'] = $vo['Temperature3'] + $vehicle_obj['temp_limit_offset'];
                                }


                            }
                        }
                    }
                    return $data_arr;
                }

        }
    }

    public function get_vehicle_loginname($vehicle)
    {
        $obj = $this->select('loginname')->where('vehicle', $vehicle)->first();

        return ['username' => $obj['loginname'], 'password' => self::VECHICLE_CONFIG['VEHICLE_LOGINNAME'][$obj['loginname']]];

    }

    public function get_vehicle_address($lon, $lat)
    {
        $url = self::VECHICLE_CONFIG['VEHICLE_GETPOSITION'];
        //定义传递的参数数组；
        $data['lon'] = $lon;
        $data['lat'] = $lat;
        //定义返回值接收变量；
        $client = new Client();
        $httpstr = $client->request('GET', $url, [
            'headers' => ['Content-type' => 'text/html; charset=utf-8'],
            'query' => $data
        ]);
        $rs_obj = json_decode($httpstr->getBody()->getContents());
        $data = $rs_obj->obj;
        if ($rs_obj->error == '')
            return $data->data;
        else return '';
    }
    static public function fieldTitles()
    {
        return [
            'vehicle_id' => 'ID',
            'vehicle' => '设备名',
            'gps_time' => 'GPS上传时间',
            'address' => '位置',
            'refresh_time' => '刷新时间',
            'install_time' => '安装时间'
        ];
    }

    public function getRefreshTimeAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }

    public function getInstallTimeAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }

}
