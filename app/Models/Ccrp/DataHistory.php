<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;

class DataHistory extends Model
{
    protected $connection = 'dbhistory';
    protected $table = '"sensor"."10000000"';
    protected $prefix = '';
    protected $primaryKey = 'data_id';

    public  function tableName($sensor){
        $this->setTable( '"sensor"."'.$sensor.'"');
    }
    public function SensorData($start_time,$end_time)
    {
            return $this->where('sensor_collect_time','between',[$start_time,$end_time])->select();
    }
    public function getSensorCollectTimeAttr($value)
    {
        return date('Y-m-d H:i',$value);
    }
}
