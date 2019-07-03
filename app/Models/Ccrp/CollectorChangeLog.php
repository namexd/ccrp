<?php

namespace App\Models\Ccrp;

use App\Traits\ModelFields;
use PrinterAPI;

class CollectorChangeLog extends Coldchain2Model
{
    protected $table = 'collector_changelog';
    protected $fillable = [
        'collector_id',
        'collector_name',
        'cooler_id',
        'cooler_name',
        'supplier_id',
        'supplier_collector_id',
        'new_supplier_collector_id',
        'category_id',
        'company_id',
        'change_note',
        'change_time',
        'change_option',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class, 'collector_id', 'collector_id');
    }

}
