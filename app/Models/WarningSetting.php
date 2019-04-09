<?php

namespace App\Models;
class WarningSetting extends Coldchain2Model
{
    protected $table='warning_setting';

    function collector()
    {
        return $this->belongsTo('collector');
    }

}
