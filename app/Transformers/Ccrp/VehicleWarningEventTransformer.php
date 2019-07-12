<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Vehicle;
use App\Models\Ccrp\VehicleWarningEvent;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class VehicleWarningEventTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['to_vehicle','warniger'];

    public function transform(VehicleWarningEvent $vehicle)
    {
        $result = [
            'id'=>$vehicle->id,
            'vehicle_id'=>$vehicle->vehicle_id,
            'vehicle'=>$vehicle->vehicle,
            'warninger_id'=>$vehicle->warninger_id,
            'which'=>$vehicle->which,
            'temp'=>$vehicle->temp,
            'type'=>$vehicle->type==1?'高温':'低温',
            'create_time'=>$vehicle->create_time>0?Carbon::createFromTimestamp($vehicle->create_time)->toDateTimeString():0,
        ];
        return $result;
    }


    public function includeToVehicle(VehicleWarningEvent $vehicle)
    {
        return $this->item($vehicle->to_vehicle, new VehicleTransformer());
    }
    public function includeWarniger(VehicleWarningEvent $vehicle)
    {
        return $this->item($vehicle->warniger, new WarningerTransformer());
    }
}