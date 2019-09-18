<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Printer;
use App\Models\Ccrp\PrinterLog;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrinterLogDetailTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['printer'];
    public function transform(PrinterLog $printer_log)
    {
        $result = [
            'id'=>$printer_log->id,
            'title'=>$printer_log->title,
            'subtitle'=>$printer_log->subtitle,
            'content'=>$printer_log->content,
            'print_time'=>$printer_log->print_time>0?Carbon::createFromTimestamp($printer_log->print_time)->toDateTimeString():0,
            'company_id'=>$printer_log->company_id,
            'uid'=>$printer_log->uid,
            'orderindex'=>$printer_log->orderindex,
            'server_state'=>$printer_log->server_state,
            'order_state'=>$printer_log->order_state,
            'order_status'=>$printer_log->order_status,
            'pages'=>$printer_log->pages,
            'pagei'=>$printer_log->pagei,
            'from_type'=>$printer_log->from_type,
            'from_device'=>$printer_log->from_device,
            'from_order_id'=>$printer_log->from_order_id,
            'from_time_begin'=>$printer_log->from_time_begin>0?Carbon::createFromTimestamp($printer_log->from_time_begin)->toDateTimeString():0,
            'from_time_end'=>$printer_log->from_time_end>0?Carbon::createFromTimestamp($printer_log->from_time_end)->toDateTimeString():0,
            'sign_id'=>$printer_log->sign_id,
            'sign_time'=>$printer_log->sign_time>0?Carbon::createFromTimestamp($printer_log->sign_time)->toDateTimeString():0,
        ];
        return $result;
    }

    public function includePrinter(PrinterLog $printerLog)
    {
        return $this->item($printerLog->printer, new PrinterTransformer());
    }

}