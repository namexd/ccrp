<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\DataHistory;
use function App\Utils\abs2;
use function App\Utils\to_shidu;
use function App\Utils\to_wendu;
use DB;

class TemperatuesCoolerHistory extends Coldchain2Model
{
    //http://export.coldyun.net/index/files/export?method=get&Customerid=605&Customername=莘庄&Userid=664&Billno=莘庄_莘庄3101120101-05-0001_2019年02月数据一览表&GetdataUrl=bGxfc2VydjIwL2FwaXNlcnZlci9jb29sZXJfaGlzdG9yeV8zMC9jb29sZXJfaWQvMTg4OS9zdGFydC8yMDE5LTAy
    public function getExportUrl($coolers, $user_id, $month)
    {
        $date = date('Y年m月', strtotime($month . '-01 00:00:01'));
        $url = 'http://export.coldyun.net/index/files/export?method=get&';
        foreach ($coolers as $key => &$cooler) {
            $requestinfo = [
//            'Guid' => '',                 //服务流水id，自动生成(默认空）
//            'System' => '冷链监测系统2.0',  //系统信息（请传入项目名称（默认：冷链监测系统2.0）
//            'Servicetype' => 'Pdfx',       //服务类型：Excel,Pdf,Pdfx（可选，默认Excel）
//            'Servicename' => 'Default',   //通用导出模板，（可选[Default]，默认Default）
//            'Serviceid' => 0,             //服务ID，默认0（不可修改）
                'Customerid' => $cooler->company_id,          //单位id，用于回调
                'Customername' => $cooler->company->title,      //单位名称，用于LOG
                'Userid' => $user_id,              //用户id，用于LOG
//                'CommandText' => '',          //sql语句获取数据（暂未开放，默认空）
//                'Colsname' => '',              //json数组（请遵循漏下格式，默认空）
                'Billno' => $cooler->company->title . '_' . $cooler->cooler_name . '_' . $date . '数据一览表',       //订单/运单/文件名
//            'DatasetJson' => '',           //json数组，默认空
                'GetdataUrl' => base64_encode(config('api.domain').'/api/ccrp/reports/temperatures/coolers_history_30/' . $cooler->cooler_id . '/' . $month), //URL获取数据的URL，（推荐，传入为base64加密，默认空）
            ];
            $params = http_build_query($requestinfo);
            $cooler->url=$url.$params;
        }
        return $coolers;
    }

    public function getCoolerHistory30($cooler_id, $moth)
    {
        $month_first = date('Y-m-01 00:00:00', strtotime($moth));
        $month = date('Y-m', strtotime($moth));
        $month_last = date('Y-m-d H:i:s', strtotime(date('Y-m-01', strtotime($month_first)) . ' +1 month') - 1);  //-1
        $month_start = strtotime($month_first);
        $month_end = strtotime($month_last);


        //init 30 clock
        $clock = array();
        for ($i = date('d', $month_start) + 0; $i <= date('d', $month_end) + 0; $i++) {
            for ($j = 0; $j < 24; $j++) {
                $sharp_clock = $month . '-' . sprintf('%02s', $i) . ' ' . sprintf('%02s', $j) . ':00:00';
                $sharp_time = strtotime($month . '-' . sprintf('%02s', $i) . ' ' . sprintf('%02s', $j) . ':00:00');
                $clock[] = array('sensor' => '', 'time' => $sharp_clock, "temp" => NULL, "humi" => NULL);

                $sharp_clock = $month . '-' . sprintf('%02s', $i) . ' ' . sprintf('%02s', $j) . ':30:00';
                $sharp_time = strtotime($month . '-' . sprintf('%02s', $i) . ' ' . sprintf('%02s', $j) . ':00:00');
                $clock[] = array('sensor' => '', 'time' => $sharp_clock, "temp" => NULL, "humi" => NULL);
            }
        }
        $cooler = Cooler::find($cooler_id);


        //如果采集的数据第一条早于安装时间，则取安装时间
        if ($cooler['install_time'] > $month_start)
            $month_start = $cooler['install_time'];

        if ($cooler['uninstall_time'] > $month_start and $cooler['uninstall_time'] < $month_end)
            $month_end = $cooler['uninstall_time'];


        if ($cooler) {
            $config = array();
            $data = array();
            $mapi = array('cooler_id' => $cooler_id);
            $collectors = Collector::whereRaw('(uninstall_time = 0 or uninstall_time >' . $month_start . ') and ( install_time <' . $month_end . ')')->select(DB::raw('supplier_collector_id,collector_name,temp_fix,install_time,uninstall_time'))->where($mapi)->orderBy('collector_name', 'asc')->get();

            $history = new DataHistory();
            foreach ($collectors as $key => &$collector) {

                if ($collector['install_time'] > $month_start)
                    $_start = $collector['install_time'];
                else
                    $_start = $month_start;

                if ($collector['uninstall_time'] > $month_start and $collector['uninstall_time'] < $month_end)
                    $_end = $collector['uninstall_time'];
                else {
                    $_end = $month_end;
                }

                $table = "sensor." . abs2($collector['supplier_collector_id']);
                $pgModel = $history->setTable($table);
                $collector['data'] = $pgModel->select(DB::raw('sensor_id,(temp+' . $collector['temp_fix'] . ') as temp,humi,sensor_collect_time,sender_trans_time'))->whereBetween('sensor_collect_time', [intval($_start), intval($_end)])->orderBy('sensor_collect_time', 'asc')->get();

                //temp_fixed
                $collector['data_count'] = count($collector['data']);//数据数量
                if ($collector['data_count'] == 0) {
                    unset($collector);
                } else {
                    $cclock = $clock;
                    //fit it to clock array
                    $jj = 0;

                    foreach ($cclock as $kk => &$cl) {
                        $cl['sensor'] = $collector['collector_name'];
                        $crr = strtotime($cl['time']);
                        $vo = null;
                        $ii = 0;
                        $jj++;
//                        array_unshift($collector['data'],array('探头','时间','温度','湿度'));
                        foreach ($collector['data'] as $k => $vo) {
                            $ii++;
                            if ($cl['temp'] == NULL and $vo['sensor_collect_time'] > $crr and $vo['sensor_collect_time'] - $crr <= 30 * 60) {
                                $cl['temp'] = to_wendu($vo['temp'], '');
                                $cl['humi'] = to_shidu($vo['humi'], '');
                                $cl['time'] = date('Y-m-d H:i:s', $vo['sensor_collect_time']);
                                $cl['trans_time'] = date('Y-m-d H:i:s', $vo['sender_trans_time']);
                                unset($collector['data'][$k]);
                            } elseif ($cl['temp'] != NULL and $vo['sensor_collect_time'] > $crr and $vo['sensor_collect_time'] - $crr <= 30 * 60) {
                                unset($collector['data'][$k]);

                            } elseif ($vo['sensor_collect_time'] - $crr > 30 * 60) {
                                break;
                            } else {
                                continue;
                            }
                        }
                    }
                    array_unshift($cclock, array('sensor' => '探头', 'time' => '采集时间', 'trans_time' => '上传时间', 'temp' => '温度', 'humi' => '湿度'));
                    $sheet_name = '(' . ($key + 1) . ')' . abs2($collector['supplier_collector_id']);
                    $data['Sheet_' . $sheet_name] = $cclock;
                    $config['config'][] = array(
                        'sheet_name' => $sheet_name,
                        'sheet_title' => $cooler['cooler_name'],
                        'sheet_subtitle' => '',
                        'sheet_desc' => '',
                    );
                }


            }
//            sortArrByField($collectors,'data_count');//排序

        }
        foreach ($data as $k => $d) {
            $config[$k] = $d;
        }
        return $config;
    }
}
