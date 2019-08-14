<?php

namespace App\Models\Ccrp;
class CollectorHasCertification extends Coldchain2Model
{

    protected $fillable = ['id', 'collector_id', 'certification_id'];

    function collector()
    {
        return $this->belongsTo(Collector::class);
    }
    function certification()
    {
        return $this->belongsTo(Certification::class);
    }
}
