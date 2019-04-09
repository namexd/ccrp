<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WarningEventRequest;
use App\Models\WarningEvent;
use App\Models\WarningSenderEvent;
use App\Traits\ControllerDataRange;
use App\Transformers\WarningAllEventTransformer;
use App\Transformers\WarningEventTransformer;
use App\Transformers\WarningSenderEventTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

class WarningEventsController extends Controller
{
    use ControllerDataRange;
    public $default_date = '最近30天';

    public function index($handled)
    {
        $this->check();
        $model = WarningEvent::whereIn('company_id', $this->company_ids);
        switch ($handled) {
            case 'unhandled':
                $this->default_date = '全部';
                $model = $model->where('handled', WarningEvent::未处理);
                break;
            case 'handled':
                $model = $model->where('handled', WarningEvent::已处理);
                break;
            default  :
        }
        $this->set_default_datas($this->default_date);
        $model = $model->whereBetween(WarningEvent::TIME_FIELD, $this->get_dates());
        $evnets = $model->orderBy('id', 'desc')->paginate($this->pagesize);
        return $this->response->paginator($evnets, new WarningEventTransformer())->addMeta('date_range', $this->get_dates('datetime', true));
    }

    public function show($event)
    {
        $this->check();
        $event = WarningEvent::whereIn('company_id', $this->company_ids)->find($event);
        if ($event) {
            if ($event->handled == 0) {
                return $this->response->item($event, new WarningEventTransformer())->addMeta('user', $this->user());
            } else {
                return $this->response->item($event, new WarningEventTransformer());
            }
        }
        return $this->response->noContent();
    }

    public function update(WarningEventRequest $request, $event)
    {
        $this->check();
        $event = WarningEvent::whereIn('company_id', $this->company_ids)->find($event);
        $event->handled = 1;
        $event->handler = $request->handler;
        $event->handler_note = $request->handler_note;
        $event->handled_time = time();
        $event->save();
        return $this->response->item($event, new WarningEventTransformer());
    }

}
