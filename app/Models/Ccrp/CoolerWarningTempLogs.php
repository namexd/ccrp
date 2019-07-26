<?php

namespace App\Models\Ccrp;


class CoolerWarningTempLogs extends Coldchain2ModelWithTimestamp
{
    protected $fillable = [
        'cooler_id',
        'warning_time',
        'company_id'
    ];
    protected $table = 'cooler_warning_temp_logs';
    protected $dates = ['warning_time'];

    public function cooler()
    {
        $this->belongsTo(Cooler::class, 'cooler_id', 'cooler_id');
    }
}
