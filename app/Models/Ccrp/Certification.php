<?php

namespace App\Models\Ccrp;
class Certification extends Coldchain2Model
{
    protected $fillable = [
        'id',
        'certificate_no',
        'certificate_year',
        'out_date',
        'customer',
        'customer_address',
        'instrument_name',
        'manufacturer',
        'instrument_model',
        'instrument_no',
        'instrument_accuracy',
        'file_id',
        'file_ids',
        'pay_company_id',
        'company_id',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    //额外的所有文件
    public function files()
    {
        $ids = explode(',', $this->file_ids);
        return File::whereIn('id', $ids)->get();
    }

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    function payCompany()
    {
        return $this->belongsTo(Company::class, 'pay_company_id', 'id');
    }

}
