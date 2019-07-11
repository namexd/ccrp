<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Printer;
use App\Models\Ccrp\PrinterLog;
use League\Fractal\TransformerAbstract;

class PrinterLogTransformer extends TransformerAbstract
{
    private $columns = [
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

    public function columns()
    {

    }

    public function transform(PrinterLog $printer_log)
    {
        $result = [];
        foreach ($this->columns as $column) {
            $result[$column] = $printer_log->{$column} ?? '';
        }
        return $result;
    }


}