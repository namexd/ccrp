<?php


namespace App\Models\Ccrp;


class VaccineTags extends Coldchain2Model
{
    protected $table='vaccine_tags';

    public function getCategory()
    {
        return $this->selectRaw('substring_index(name,"-",1) as name')->groupBy(\DB::raw('substring_index(name,"-",1)'))->pluck('name');
    }

    public function cooler()
    {
        return $this->belongsToMany(Cooler::class,'cooler_has_vaccine_tags','tag_id','cooler_id');
    }

}