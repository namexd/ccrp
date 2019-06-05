<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\PrinterRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Printer;
use App\Models\Ccrp\Vehicle;

use App\Models\Ccrp\PrinterTemplate;
use App\Transformers\Ccrp\PrinterTransformer;


class PrintersController extends Controller
{
    private $printer;

    public function __construct(Printer $printer)
    {
        $this->printer = $printer;
    }

    public function index()
    {
        $this->check();
        $vehicles = $this->printer->whereIn('company_id', $this->company_ids)->where('status', 1)
            ->paginate($this->pagesize);
        $transform = new PrinterTransformer();
        return $this->response->paginator($vehicles, $transform);
    }

    public function printTemp(PrinterRequest $request, Vehicle $vehicleModel, Collector $collectorModel, PrinterTemplate $printTemplate)
    {

        $this->check();
        if ($request->has('start') and $request->has('end')) {
            $start = $request->start;
            $end = $request->end;
        } else {
            $start = date('Y-m-d H:i:s', time() - 3600);
            $end = date('Y-m-d H:i:s', time());
        }
        if ($request->has('id') || $request->has('vehicle')) {
            $lists = $vehicleModel->vehicle_temp($request->all(), $start, $end);
            $title = $request->has('vehicle') ? $request->vehicle : $vehicleModel->find($request->id)->vehicle;
        }
        if ($request->has('collector_id')) {
            $collector = $collectorModel->find($request->collector_id);
            $lists = $collector->history(strtotime($start),strtotime($end))->toArray();
            $title = $collector->collector_name.'('.$collector->supplier_collector_id.')';
        }
        $subtitle=$request->subtitle??null;
        $summary=$request->summary??'';
        $id = $request->printer_id ?? 1;
        $type = $request->type ?? 'vehicle';
        $from = array();
        $from['from_type'] = $type.'/temp';
        $from['from_device'] = $title;
        $from['from_order_id'] = 0;
        $from['from_time_begin'] = strtotime($start);
        $from['from_time_end'] = strtotime($end);
        //大于60条，拆分成多条打印
        $count_arr = count($lists);
        if ($count_arr > 60) {
            $big_arr = array_chunk($lists, 60);

            $pages = count($big_arr);
            $pagei = 0;
            foreach ($big_arr as $data_arr_i) {

                //from 增加分页
                $from['pages'] = $pages;
                $from['pagei'] = ++$pagei;

                $resp[] = $this->printer->printer_print_array($id, $title, $printTemplate->default($type, $title, $data_arr_i, $this->company->id,$subtitle,$summary), $this->user()->id, $subtitle, $from);
            }
        } else {
            $resp[] = $this->printer->printer_print_array($id, $title, $printTemplate->default($type, $title, $lists, $this->company->id,$subtitle, $summary), $this->user()->id, $subtitle, $from);
        }
        return $this->response->array($resp);
    }
}
