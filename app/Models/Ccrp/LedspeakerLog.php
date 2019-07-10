<?php

namespace App\Models\Ccrp;

/**
 * Class Collector
 * @package App\Models
 */
class LedspeakerLog extends Coldchain2Model
{
    protected $table = 'ledspeaker_changelog';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ledspeaker_id',
        'ledspeaker_name',
        'supplier_id',
        'supplier_ledspeaker_id',
        'new_supplier_ledspeaker_id',
        'category_id',
        'company_id',
        'change_note',
        'change_time',
        'change_option',

    ];

}
