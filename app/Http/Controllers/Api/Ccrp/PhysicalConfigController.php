<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\DeliverLog;
use App\Models\Ccrp\PhysicalConfig;
use App\Transformers\Ccrp\PhysicalConfigTransformer;

class PhysicalConfigController extends Controller
{
    private $model;

    public function __construct(PhysicalConfig $config)
    {
        $this->model = $config;
    }

    public function index()
    {
        $this->check();
        $config = $this->model->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($config, new PhysicalConfigTransformer());
    }

}
