<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Vehicle;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class VehicleTransformer extends TransformerAbstract
{
    protected $availableIncludes=['has_warninger'];
    private $columns = [
        'vehicle_id' ,
        'vehicle' ,
        'gps_time',
        'address' ,
        'temperature' ,
        'temperature2' ,
        'temperature3' ,
        'temperature_warning_open' ,
        'refresh_time',
        'install_time'
    ];

    public function columns()
    {
        //获取字段中文名
        return Vehicle::getFieldsTitles($this->columns);
    }

    public function transform(Vehicle $vehicle)
    {
        $result=[];
        foreach ($this->columns as $column)
        {
            $result[$column]=$vehicle->{$column}??'';
        }
        return $result;
    }


    public function includeHasWarninger(Vehicle $vehicle)
    {
        if ($vehicle->warninger)
        return $this->item($vehicle->has_warninger,new WarningerTransformer());
        else
            return new Item(null,function (){
                return [];
            });
    }
}