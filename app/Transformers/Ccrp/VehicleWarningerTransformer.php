<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Vehicle;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class VehicleWarningerTransformer extends TransformerAbstract
{
    protected $availableIncludes=['has_warninger'];
    private $columns = [
        'vehicle',
        'temperature_warning_open',
        'warninger',
        'temp_low',
        'temp_high',
        'temp2_low',
        'temp2_high',
        'temp3_low',
        'temp3_high',
    ];


    public function transform(Vehicle $vehicle)
    {
        $result = [];
        foreach ($this->columns as $column) {
            $result[$column] = $vehicle->{$column} ?? '';
        }
        return $result;
    }


    public function includeHasWarninger(Vehicle $vehicle)
    {
        if ($vehicle->warninger)
            return $this->item($vehicle->has_warninger, new WarningerTransformer());
        else
            return new Item(null, function () {
                return [];
            });
    }
}