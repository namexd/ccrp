<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Printer;
use League\Fractal\TransformerAbstract;

class PrinterTransformer extends TransformerAbstract
{
    private $columns = [
        'printer_id' ,
        'printer_sn' ,
        'printer_name',
        'vehicle' ,
        'printer_simcard',
        'company_id',
        'install_uid',
        'install_time',
        'update_time',
        'refresh_time',
        'server_status',
        'job_done',
        'job_waiting',
        'status',
    ];

    public function columns()
    {

    }

    public function transform(Printer $printer)
    {
        $result=[];
        foreach ($this->columns as $column)
        {
            $result[$column]=$printer->{$column}??'';
        }
        return $result;
    }


}