<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Extensions\Feie\PrinterAPI;
use App\Http\Requests\Api\Ccrp\Setting\PrinterRequest;
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
        $printers = $this->printer->whereIn('company_id', $this->company_ids)->where('status', 1);
        if ($keyword = request()->get('keyword')) {
            $printers = $printers->where('printer_name', 'like', '%'.$keyword.'%')
                ->whereOr('printer_sn', 'like', '%'.$keyword.'%');
        }
        if ($this->user->userlevel == 2) {
            if ($this->user->binding_printer <> '')
                $printers = $printers->whereIn('printer_sn', explode(',', $this->user->binding_printer));
        }
        $printers = $printers->paginate(request()->get('pagesize') ?? $this->pagesize);
        $printer = new PrinterAPI();
        $printer->IP = $this->printer::CONFIG['PRINTER_IP'];
        $printer->PORT = $this->printer::CONFIG['PRINTER_PORT'];
        $printer->HOSTNAME = $this->printer::CONFIG['PRINTER_HOSTNAME'];
        foreach ($printers as &$vo) {
            $rs = $printer->queryPrinterStatus($vo['printer_sn'], $vo['printer_key']);

            $rs = json_decode($rs);

            if ($rs->responseCode == 0) {
                $vo['refresh_time'] = $data['refresh_time'] = time();

                $vo['server_status'] = $data['server_status'] = $rs->msg;

                $rs = $printer->queryOrderInfoByDate($vo['printer_sn'], $vo['printer_key'], date('Y-m-d'));
                $rs = json_decode($rs);
                if ($rs->responseCode == 0) {
                    $vo['job_done'] = $data['job_done'] = $rs->print;
                    $vo['job_waiting'] = $data['job_waiting'] = $rs->waiting;
                }

                $this->printer->where('printer_id', $vo['printer_id'])->update($data);

            }

        }
        $transform = new PrinterTransformer();
        return $this->response->paginator($printers, $transform);
    }

    public function show($id)
    {
        $printer = $this->printer->find($id);
        return $this->response->item($printer, new PrinterTransformer);
    }

    public function store(PrinterRequest $request)
    {
        $this->check();
        //虚拟打印机00000000
        if ($request->get('printer_sn') != '00000000') {
            if ($this->printer->where('printer_sn', $request->get('printer_sn'))->first()) {
                return $this->response->errorBadRequest('设备序号被占用了!');
            }
            //验证打印机
            $printer = new PrinterAPI();
            $printer->IP = $this->printer::CONFIG['PRINTER_IP'];
            $printer->PORT = $this->printer::CONFIG['PRINTER_PORT'];
            $printer->HOSTNAME = $this->printer::CONFIG['PRINTER_HOSTNAME'];
            $rs = $printer->queryPrinterStatus($request->get('printer_sn'), $request->get('printer_key'));
            $rs = json_decode($rs);
            if ($rs->responseCode == 1) {
                return $this->response->errorBadRequest('设备号/密匙不正确');
            }
        }
        $request['company_id'] = $this->company->id;
        $request['status'] = 1;
        $request['install_uid'] = $this->user->id;
        $request['install_time'] = time();
        $result = $this->printer->create($request->all());

        if ($result) {
            return $this->response->item($result, new PrinterTransformer());
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

    public function update($id)
    {
        $this->check();
        $printer = $this->printer->find($id);
        $request = request()->all();
        $request['update_time'] = time();
        $printer->update($request);
        return $this->response->item($printer, new PrinterTransformer());
    }

    public function destroy($id)
    {
        $printer = $this->printer->find($id);
        $printer->delete();
        return $this->response->noContent();
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
            $lists = $collector->history(strtotime($start), strtotime($end))->toArray();
            $title = $collector->supplier_collector_id;
        }
        $subtitle = $request->subtitle ?? null;
        $summary = $request->summary ?? '';
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

                $resp[] = $this->printer->printer_print_array($id, $title, $printTemplate->default($type, $title, $data_arr_i, $this->company->id, $subtitle, $summary), $this->user->id, $subtitle, $from);
            }
        } else {
            $resp[] = $this->printer->printer_print_array($id, $title, $printTemplate->default($type, $title, $lists, $this->company->id, $subtitle, $summary), $this->user->id, $subtitle, $from);
        }
        return $this->response->array($resp);
    }

    public function test($id)
    {
        $this->check();
        $resp= $this->printer->printer_print_array($id, '测试打印', '', $this->user->id, '');
        return $this->response->array($resp);
    }
}
