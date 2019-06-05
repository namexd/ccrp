<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\WarningSendlog;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningSendlogTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['device'];

    public function transform(WarningSendlog $log)
    {
        $result['id'] = $log->id;
        $result['event_type'] = $log->event_type;
        $result['event_level'] = $log->event_level;
        $result['event_value'] = $log->event_value;
        $result['msg_type'] = $log->event_value;
        $result['send_to'] = $log->send_to;
        $result['content'] = $log->send_content_all;
        $result['company'] = $log->company->title;
        $result['creat_time'] =  Carbon::createFromTimestamp($log->send_time)->toDateTimeString();
        return $result;
    }

    public function includeDevice(WarningSendlog $log)
    {
        if($log->collector_id > 0)
        {
            return $this->item($log->collector, new CollectorTransformer());
        }elseif($log->sender_id >0)
        {
            return $this->item($log->sender, new SenderTransformer());
        }else{
            return null;
        }
    }
}