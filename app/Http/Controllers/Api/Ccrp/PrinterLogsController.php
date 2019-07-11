<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\PrintLogRequest;
use App\Models\Ccrp\PrinterLog;

use App\Models\Ccrp\PrintLogTemplate;
use App\Transformers\Ccrp\PrintLogTransformer;


class PrinterLogsController extends Controller
{
    private $model;

    public function __construct(PrinterLog $printer)
    {
        $this->model = $printer;
    }

    public function index()
    {
        $this->check();
        $vehicles = $this->model->whereIn('company_id', $this->company_ids)->where('status', 1)
            ->paginate(request()->get('pagesize')??$this->pagesize);
        $transform = new PrintLogTransformer();
        return $this->response->paginator($vehicles, $transform);
    }

}
