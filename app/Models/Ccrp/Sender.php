<?php

namespace App\Models\Ccrp;


class Sender extends Coldchain2Model
{
    protected $table='sender';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
