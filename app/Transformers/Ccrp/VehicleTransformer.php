<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Vehicle;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class VehicleTransformer extends TransformerAbstract
{
    private $columns = [
        'vehicle_id' ,
        'vehicle' ,
        'gps_time',
        'address' ,
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


}