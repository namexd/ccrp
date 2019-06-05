<?php

namespace App\Models\Ccrp;
class WarningSetting extends Coldchain2Model
{
    protected $table='warning_setting';

    function collector()
    {
        return $this->belongsTo('collector');
    }

}
