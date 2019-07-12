<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\VehicleWarningEvent;
use App\Transformers\Ccrp\VehicleWarningEventTransformer;


class VehicleWarningEventsController extends Controller
{
    private $model;

    public function __construct(VehicleWarningEvent $event)
    {
        $this->model = $event;
    }

    public function index()
    {
        $this->check();
        $vehicles = $this->model;
        if ($vehicle_id= request()->get('vehicle_id')) {
            $vehicles = $vehicles->where('vehicle_id', $vehicle_id);
        }
        $vehicles = $vehicles->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($vehicles, new VehicleWarningEventTransformer());
    }

}
