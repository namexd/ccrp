<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\DataHistory;
use App\Models\Ccrp\WarningSetting;
use App\Traits\ModelFields;
use function App\Utils\getDays;
use function App\Utils\time_format;
use DB;

class StatCoolerHistoryTemp extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'stat_cooler_history_temp';

    public function company()
    {
        $rs = $this->belongsTo(Company::class);
        return $rs;
    }

    static public function fieldTitles()
    {
        return [

        ];
    }

    public function getTemp($start, $end, $point, $cooler_id)
    {
        $data['status'] = 'ok';

        //create time array
        $_start = $start . ' ' . $point[0] . ':00:00';
        $_end = $end . ' ' . end($point) . ':00:00';

        $time_start = strtotime($_start);
        $time_end = strtotime($_end);
        $days = floor(($time_end - $time_start) / 3600 / 24); //间隔天数
        for ($i = 0; $i <= $days; $i++) {
            foreach ($point as $vo) {
                //$day_array[] = date('Y-m-d H:00:00',strtotime($start.' '.$vo.':00:00' . '+'.$i.' day'));
                //$day_key[] = date('YmdH',strtotime($start.' '.$vo.':00:00' . '+'.$i.' day'));
                $day_point[date('Y-m-d', strtotime($start . ' ' . $vo . ':00:00' . '+' . $i . ' day'))][$vo] = date('YmdH', strtotime($start . ' ' . $vo . ':00:00' . '+' . $i . ' day'));
            }
            unset($vo);
        }
        $cmap ['cooler_id'] = $cooler_id;
//        DB::connection('dbyingyong')->enableQueryLog();

        $collectors = Collector::where($cmap)->whereRaw('(uninstall_time = 0 or uninstall_time >' . $time_start . ') and ( install_time <' . $time_end . ')')->orderBy('collector_name', 'asc')->get();
        // echo M()->getlastsql();
        foreach ($collectors as &$collector) {
            if ($collector['status'] == 2 and (strtotime($end) > $collector['uninstall_time'])) {
                $theend = date('Y-m-d', $collector['uninstall_time']);
            } else {
                $theend = $end;
            }
            if ($collector['install_time'] > $start) {
                $thestart = date('Y-m-d', $collector['install_time']);
            } else {
                $thestart = $start;
            }
//            $collector_id = $collector['supplier_id'] . '|' . abs2($collector['supplier_collector_id']) . '|' . $collector['collector_id'];//1003|15210000074|3075
//                 dump($collector_id);die();

            $data['data'] = $this->pointdata_ok($collector, $thestart, $theend, $point);
            $wmap['collector_id'] = $collector['collector_id'];
            $wmap['temp_warning'] = 1;
            $wmap['status'] = 1;
//            $collector_warninger_setting = WarningSetting::where($wmap)->first();
//            foreach ($data['data'] as $key => &$vo) {
//                if ($collector['temp_fix'])
//                    $vo += $collector['temp_fix'];
//                if ($collector_warninger_setting) {
//                    if(Input::get('test')==1){
//                        if ($vo > $collector_warninger_setting['temp_high']) {
//                            $vo = '<a href="/pg/cooler_history_temp_update?collector_id=' . $collector['collector_id'] . '&time=' . $key . '" style="color:red;font-weight:bold;" target="_blank" class="btn btn-xs btn-primary ajax-get2">' . $vo . '</a>';
//                        } elseif ($vo < $collector_warninger_setting['temp_low']) {
//                            $vo = '<a href="/pg/cooler_history_temp_update?collector_id=' . $collector['collector_id'] . '&time=' . $key . '" style="color:blue;font-weight:bold;"  target="_blank" class="btn btn-xs btn-primary  ajax-get2">' . $vo . '</a>';
//                        }
//                    }else{
//                        if ($vo > $collector_warninger_setting['temp_high']) {
//                            $vo = '<b  style="color:red;font-weight:bold;" >' . $vo . '</a>';
//                        } elseif ($vo < $collector_warninger_setting['temp_low']) {
//                            $vo = '<b style="color:blue;font-weight:bold;" >' . $vo . '</a>';
//                        }
//                    }
//                }
//
//            }
            unset($vo);
            $collector['data'] = $data['data'];
            $collector['data_count'] = count($data['data']);
        }
        unset($collector);
        $result = array();
        foreach ($day_point as $key => $vo) {
            foreach ($vo as $k => $v) {
                foreach ($collectors as $vv => $collector) {
                    if (array_key_exists($v, $collector['data'])) {
                        $result[$key][$k][] = $collector['data'][$v];
                    } else {
                        $result[$key][$k][] = '-';
                    }
                }
            }
        }
        return $result;
    }

    public function pointdata_ok($collector, $start, $end, $point)
    {
        $sensor_id = $collector['supplier_collector_id'];
        $the_collector = $collector;
        //切换表
        $supplier_collector_id = str_replace('-', '', $the_collector['supplier_collector_id']);
        $history = new DataHistory();


        $where['sensor_id'] = $supplier_collector_id;
        $table = '"sensor".'. '"'.$supplier_collector_id.'"';
        $table2 = "sensor." . $supplier_collector_id;
        $pgModel = $history->setTable($table2);
        $day = $start;
        $start = strtotime('' . $start . ' 00:00:00');
        $end = strtotime('' . $end . ' 23:59:59');
        //如果采集的数据第一条晚于截止时间，返回空
        $first = $pgModel->first();
        if ($first['sensor_collect_time'] > $end) return [];

        //如果采集的数据第一条晚于起始时间，其实时间=采集第一条的时间
        if ($first['sensor_collect_time'] > $start)
            $start = $first['sensor_collect_time'];

        //如果采集的数据第一条早于安装时间，则取安装时间
        if ($the_collector['install_time'] > $start)
            $start = $the_collector['install_time'];

        for ($i = 0; $i < count($point); $i++) {
            $point[$i] = intval($point[$i]);
        }

        $have_logs_map['collector_id'] = $collector['collector_id'];

//        $months_num = getMonthNum(time_format($start),time_format($end));
//        $months = getLastMonths($months_num,date('Ym',$end),'Ym');


//        $have_logs_map['year'] = date("Y", $start);
//        $have_logs_map['month'] = array('in',array(date("m", $start),date("m", $end)));
//        $have_logs_map['CONCAT(year,LPAD(month,2,0))'] = array('in',$months);

        $days = getDays(time_format($start), time_format($end), 'Ymd');
        $point_time = array();
        foreach ($days as $day) {
            foreach ($point as $p) {
                $point_time[] = $day . str_pad($p, 2, "0", STR_PAD_LEFT);
            }
        }

        $new_pg_model = $pgModel;
        $have_logs = $this->where($have_logs_map)->whereIn('point_time', $point_time)->get();
        $where = array();
        $dd = array();
        if (!$have_logs->isEmpty()) {
            $have_points = "'0'";
            foreach ($have_logs as $po) {
                $have_points .= ",'" . $po['point_time'] . "'";
                $dd[$po['point_time']] = $po['temp'];
            }
            $new_pg_model = $new_pg_model->whereRaw("to_char(to_timestamp(sensor_collect_time), 'YYYYMMDD') ||right(concat('00', is_sharptime_h % 24), 2) not in (" . $have_points . ")");
        }

//        $where['is_sharptime_h%24'] = array('IN', $point);
//        $where['sensor_collect_time'] = array(array('gt', intval($start)), array('lt', intval($end)));
        $sql = $new_pg_model->select(DB::raw("MIN(data_id) as dataid,is_sharptime_h,to_char(to_timestamp(sensor_collect_time ), 'YYYYMMDD') as char_time"))
            ->whereRaw("is_sharptime_h%24 IN(" . implode(',', $point) . ")")->whereBetween('sensor_collect_time', [intval($start), intval($end)])->groupBy("is_sharptime_h","char_time");
//        $sql2 = " SELECT to_char(to_timestamp(sensor_collect_time+1800), 'YYYYMMDD') ||right(concat('00', rg.is_sharptime_h % 24), 2) as char_time  ,rg.is_sharptime_h,dt.temp,dt.sensor_collect_time FROM ".$table." as dt," .$sql ."  as rg WHERE dt.data_id=rg.dataid and dt.sensor_id = '".$sensor_id."'   AND dt.is_sharptime_h%24 IN(".implode(',',$point).")     AND (dt.sensor_collect_time> ".(intval($start)-300)."  AND dt.sensor_collect_time< ".intval($end).") ORDER BY rg.char_time,rg.is_sharptime_h";
        $sql2 = " SELECT to_char(to_timestamp(sensor_collect_time), 'YYYYMMDD') ||right(concat('00', rg.is_sharptime_h % 24), 2) as char_time  ,rg.is_sharptime_h,dt.temp,dt.sensor_collect_time FROM " . $table . " as dt,(" . $sql->toSql() . ")  as rg WHERE dt.data_id=rg.dataid and   dt.is_sharptime_h%24 IN(" . implode(',', $point) . ")     AND (dt.sensor_collect_time> " . (intval($start) - 300) . "  AND dt.sensor_collect_time< " . intval($end) . ") ORDER BY rg.char_time,rg.is_sharptime_h";
         $rs =DB::connection('dbhistory')->select($sql2,[$start,$end]);
//        lw_dump($sql2);

        $log['company_id'] = $collector['company_id'];
        $log['cooler_id'] = $collector['cooler_id'];
        $log['collector_id'] = $collector['collector_id'];
        $log['collector_sn'] = $sensor_id;
        $log['create_time'] = time();
        $logs = array();
        foreach ($rs as $vo) {
            $dd[$vo->char_time] = $vo->temp;

            $log['year'] = date("Y", $vo->sensor_collect_time);
            $log['month'] = date("m", $vo->sensor_collect_time);
            $log['day'] = date("d", $vo->sensor_collect_time);
            $log['point'] = $vo->is_sharptime_h;
            $log['point_time'] = $vo->char_time;
            $log['temp'] = $vo->temp;
            $log['collect_time'] = $vo->sensor_collect_time;
            $logs[] = $log;
        }
        if ($logs) {
            //add to MYSQL:stat_cooler_history_temp
            try {
                $this->insert($logs);
            } catch (Exception $exception) {
                echo $exception->getMessage();
            }
        }

        unset($rs);

        return $dd;
    }
}
