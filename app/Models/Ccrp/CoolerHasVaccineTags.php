<?php


namespace App\Models\Ccrp;


class CoolerHasVaccineTags extends Coldchain2Model
{
    protected $table='cooler_has_vaccine_tags';


    public function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }
    public function tag()
    {
        return $this->belongsTo(VaccineTags::class,'tag_id','id');
    }

}