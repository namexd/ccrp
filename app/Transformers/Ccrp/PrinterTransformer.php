<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Printer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrinterTransformer extends TransformerAbstract
{

    public function transform(Printer $printer)
    {
        $result=[
            'printer_id' =>$printer->printer_id,
            'printer_sn' =>$printer->printer_sn,
            'printer_name'=>$printer->printer_name,
            'vehicle' =>$printer->vehicle,
            'printer_simcard'=>$printer->printer_simcard,
            'company_id'=>$printer->company_id,
            'install_uid'=>$printer->install_uid,
            'install_time'=>$printer->install_time>0?Carbon::createFromTimestamp($printer->install_time)->toDateTimeString():0,
            'update_time'=>$printer->update_time>0?Carbon::createFromTimestamp($printer->update_time)->toDateTimeString():0,
            'refresh_time'=>$printer->refresh_time>0?Carbon::createFromTimestamp($printer->refresh_time)->toDateTimeString():0,
            'server_status'=>$printer->server_status,
            'job_done'=>$printer->job_done,
            'job_waiting'=>$printer->job_waiting,
            'status'=>$printer->status,
        ];
        return $result;
    }

}