<?php

namespace App\Models\Ccrp;

class PrinterlogApprove extends Coldchain2Model
{
    protected $table = 'printer_log_approve';
    protected $primaryKey = 'id';
    protected $fillable = ['log_print_time','approve_result','approve_name','approve_time','approve_note'];

    public function printerlog()
    {
        return $this->belongsTo(Printerlog::class,'log_id','id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'log_company_id','id');
    }
}
