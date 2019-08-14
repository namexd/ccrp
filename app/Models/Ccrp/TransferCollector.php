<?php

namespace App\Models\Ccrp;

class TransferCollector extends Coldchain2Model
{
    protected $table = 'transfer_collectors';
    protected $fillable = [
        'transfer_id',
        'company_id',
        'cooler_id',
        'collector_id',
        'trans_data_id',
        'trans_collect_time',
        'trans_times',
        'trans_err_times',
        'trans_data',
        'status',
    ];

    function company()
    {
        return $this->belongsTo(Company::class);
    }
    function collector()
    {
        return $this->belongsTo(Collector::class,'collector_id');
    }
    function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id');
    }

    function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function getUpdatedAtColumn()
    {
        return parent::UPDATED_AT;
    }

    public function getCreatedAtColumn()
    {
        return parent::CREATED_AT;
    }

    public function setUpdatedAt($value)
    {
        return parent::UPDATED_AT;
    }

    public function setCreatedAt($value)
    {
        return parent::CREATED_AT;
    }


}
