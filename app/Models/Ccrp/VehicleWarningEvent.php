<?php


namespace App\Models\Ccrp;


class VehicleWarningEvent extends Coldchain2Model
{
    protected $table='vehicle_warning_event';

    public function warniger()
    {
        return $this->belongsTo(Warninger::class,'warninger_id','warninger_id');
    }
    public function to_vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_id','vehicle_id');
    }
}