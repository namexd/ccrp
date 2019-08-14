<?php
namespace App\Models\Ccrp;
class CeshiSensor extends Coldchain2Model
{
    protected $table = 'ceshi_sensor';
    protected $primaryKey = 'id';

    protected $fillable = [
        'sensor_id',
        'temp',
        'humi',
        'volt',
        'rssi',
        'sender_volt',
        'sender_id',
        'sender_sn',
        'sensor_collect_time',
        'sensor_trans_time',
        'sender_trans_time',
        'system_time',
        'ceshi_time'

    ];

}
