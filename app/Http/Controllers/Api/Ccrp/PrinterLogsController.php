<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\PrintLogRequest;
use App\Models\Ccrp\PrinterLog;

use App\Models\Ccrp\PrintLogTemplate;
use App\Transformers\Ccrp\PrinterLogTransformer;
use App\Transformers\Ccrp\PrintLogTransformer;


class PrinterLogsController extends Controller
{
    private $model;

    public function __construct(PrinterLog $printerLog)
    {
        $this->model = $printerLog;
    }

    public function index()
    {
        $this->check();
        $printer_logs = $this->model;
        if ($printer_id = request()->get('printer_id')) {
            $printer_logs = $printer_logs->where('printer_id', $printer_id);
        }
        if ($keyword = request()->get('keyword')) {
            $printer_logs = $printer_logs->where('title', 'like', '%'.$keyword.'%')
                ->whereOr('subtitle', 'like', '%'.$keyword.'%')
                ->whereOr('printer_id', 'like', '%'.$keyword.'%');
        }
        $printer_logs = $printer_logs->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($printer_logs, new PrinterLogTransformer);
    }

}
