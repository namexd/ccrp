<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\WarningEventRequest;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
use App\Transformers\Ccrp\WarningAllEventTransformer;
use App\Transformers\Ccrp\WarningEventTransformer;
use App\Transformers\Ccrp\WarningSenderEventTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

class WarningAllEventsController extends Controller
{
    public function categories($handled='unhandled')
    {
        $this->check($this->user());

        $count['overtemp']['name'] = '超温预警';
        $count['overtemp']['key'] = 'overtemp';
        $count['overtemp']['route'] = 'warning_events/overtemp/';
        $count['poweroff']['name'] = '断电预警';
        $count['poweroff']['key'] = 'poweroff';
        $count['poweroff']['route'] = 'warning_events/poweroff/';
        switch ($handled) {
            case 'unhandled':
                $count['overtemp']['count'] = WarningEvent::whereIn('company_id', $this->company_ids)->where('handled',0)->count();
                $count['poweroff']['count'] = WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled',0)->count();
                break;
            case 'handled':
                $count['overtemp']['count'] = WarningEvent::whereIn('company_id', $this->company_ids)->where('handled',1)->count(); $count['poweroff']['count'] = WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled',1)->count();
                break;
            default  :
                $count['overtemp']['count'] = WarningEvent::whereIn('company_id', $this->company_ids)->count();
                $count['poweroff']['count'] = WarningSenderEvent::whereIn('company_id', $this->company_ids)->count();
        }
        foreach ($count as $item)
        {
            $categories['data'][] = $item;
}
        return $this->response->array($categories);

    }


}
