<?php

namespace App\Models\Ccrp;

use App\Traits\ModelFields;
use PrinterAPI;

class PrinterLog extends Coldchain2Model
{
    protected $table = 'printer_log';

    protected $fillable = [
        'id',
        'printer_id',
        'title',
        'subtitle',
        'content',
        'print_time',
        'company_id',
        'uid',
        'orderindex',
        'server_state',
        'order_state',
        'order_status',
        'pages',
        'pagei',
        'from_type',
        'from_device',
        'from_order_id',
        'from_time_begin',
        'from_time_end',
        'sign_id',
        'sign_time',
    ];

    public function printer()
    {
        return $this->belongsTo(Printer::class, 'printer_id', 'printer_id');
    }
    public function approve()
    {
        $this->hasOne(PrinterlogApprove::class,'log_id','id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
