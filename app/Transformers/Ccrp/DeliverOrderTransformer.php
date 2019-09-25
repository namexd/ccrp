<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\DeliverOrder;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DeliverOrderTransformer extends TransformerAbstract
{

    protected $availableIncludes=['collector','company','warningSetting'];
    public function transform(DeliverOrder $deliverOrder)
    {
        $rs =  [
            'id' => $deliverOrder->deliverorder_id,
            'deliverorder' => $deliverOrder->deliverorder,
            'deliverorder_main',
            'customer_name' => $deliverOrder->customer_name,
            'collector_id' => $deliverOrder->collector_id,
            'delivervehicle' => $deliverOrder->delivervehicle,
            'deliver' => $deliverOrder->deliver,
            'deliver_goods' =>  $deliverOrder->deliver_goods,
            'create_time' => $deliverOrder->create_time>0?Carbon::createFromTimestamp($deliverOrder->create_time)->toDateTimeString():0,
            'suborder'=>$deliverOrder->suborder,
            'finished'=>$deliverOrder->finished,
            'finished_time'=> $deliverOrder->finished_time>0?Carbon::createFromTimestamp($deliverOrder->finished_time)->toDateTimeString():0,
            'finished_note'=>$deliverOrder->finished_note,
            'status'=>$deliverOrder->status,
        ];

        return $rs;
    }
    public function includeCollector(DeliverOrder $deliverOrder)
    {
        if ($deliverOrder->collector)
        return $this->item($deliverOrder->collector,new CollectorTransformer());
        else
            return $this->null();
    }
    public function includeCompany(DeliverOrder $deliver)
    {
        return $this->item($deliver->company(),new CompanyInfoTransformer());
    }

    public function includeWarningSetting(DeliverOrder $deliver)
    {
        if ($deliver->warningSetting)
        return $this->item($deliver->warningSetting,new DeliverWarningSettingTransformer());
        else
            return $this->null();
    }

}
