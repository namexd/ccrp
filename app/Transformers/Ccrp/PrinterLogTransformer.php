<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Printer;
use App\Models\Ccrp\PrinterLog;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrinterLogTransformer extends TransformerAbstract
{
    protected $availableIncludes=['printer'];
    public function transform(PrinterLog $printer_log)
    {
        $result = [
            'id'=>$printer_log->id,
            'title'=>$printer_log->title,
            'subtitle'=>$printer_log->subtitle,
            'print_time'=>$printer_log->print_time>0?Carbon::createFromTimestamp($printer_log->print_time)->toDateTimeString():0,
            'pages'=>$printer_log->pages,
        ];
        return $result;
    }

    public function includePrinter(PrinterLog $printerLog)
    {
        if ($printerLog->printer)
        return $this->item($printerLog->printer,new PrinterTransformer());
        else
            return $this->null();
    }

}