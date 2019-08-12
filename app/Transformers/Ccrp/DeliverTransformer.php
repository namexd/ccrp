<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Deliver;
use League\Fractal\TransformerAbstract;

class DeliverTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['company'];

    public function transform(Deliver $deliver)
    {
        $rs =  [
            'id' => $deliver->deliver_id,
            'deliver'=>$deliver->deliver,
            'phone'=>$deliver->phone,
            'note'=>$deliver->note,
            'company_id'=>$deliver->company_id,
            'create_uid'=>$deliver->create_uid,
            'create_time'=>$deliver->create_time,
            'status'=>$deliver->status
        ];

        return $rs;
    }
    public function includeCompany(Deliver $deliver)
    {
        return $this->item($deliver->company(),new CompanyInfoTransformer());
    }
}
