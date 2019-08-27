<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\PrintLogRequest;
use App\Models\Ccrp\PrinterLog;

use App\Models\Ccrp\PrintLogTemplate;
use App\Transformers\Ccrp\PrinterLogDetailTransformer;
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
        $printer_logs = $this->model->whereIn('company_id',$this->company_ids);
        if ($printer_id = request()->get('printer_id')) {
            $printer_logs = $printer_logs->where('printer_id', $printer_id);
        }
        if ($keyword = request()->get('keyword')) {
            $printer_logs = $printer_logs->where(function ($query) use ($keyword){
                $query->where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('subtitle', 'like', '%'.$keyword.'%')
                    ->orWhere('printer_id', 'like', '%'.$keyword.'%');
            });
        }
        $printer_logs = $printer_logs->orderBy('id','desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($printer_logs, new PrinterLogTransformer);
    }

    public function show($id)
    {
        $printer_log=$this->model->find($id);
        return $this->response->item($printer_log,new PrinterLogDetailTransformer());
    }

}
