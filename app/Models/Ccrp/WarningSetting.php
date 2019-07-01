<?php

namespace App\Models\Ccrp;
class WarningSetting extends Coldchain2Model
{
    protected $table='warning_setting';
  public $timestamps=false;
    function collector()
    {
        return $this->belongsTo('collector');
    }

    public function warninger()
    {
        return $this->belongsTo(Warninger::class,'warninger_id','warninger_id');
    }

}
