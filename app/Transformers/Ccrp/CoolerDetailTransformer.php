<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerDetail;
use App\Transformers\Ccrp\Reports\StatCoolerTransformer;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CoolerDetailTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['sys_detail'];

    public function transform(CoolerDetail $cooler)
    {
       return [
           'id' => $cooler->id,
           'company_id' => $cooler->company_id,
           'sys_id' => $cooler->sys_id,
           'value' => $cooler->value,
           'created_at' => $cooler->created_at->toDateTimeString(),
           'updated_at' => $cooler->updated_at->toDateTimeString()
       ];
    }
    public function includeSysDetail(CoolerDetail $detail)
    {
        return $this->item($detail->sys_detail,new \App\Transformers\Ccrp\Sys\CoolerDetailTransformer());
    }
}