<?php

namespace App\Models\Ccrp;

use App\Traits\ModelFields;
use PrinterAPI;

class PrinterLog extends Coldchain2Model
{
    protected $table = 'printer_log';

    public function printer()
    {
        return $this->belongsTo(Printer::class,'printer_id','printer_id');
    }

}
