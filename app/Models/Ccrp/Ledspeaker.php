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


}
