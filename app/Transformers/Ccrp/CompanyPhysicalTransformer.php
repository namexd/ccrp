<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\CompanyPhysical;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CompanyPhysicalTransformer extends TransformerAbstract
{
    public function transform(CompanyPhysical $companyPhysical)
    {
        $rs =  [
            'id' => $companyPhysical->id,
            'name'=>$companyPhysical->physical->name,
            'score'=>$companyPhysical->score,
            'detail'=>$companyPhysical->detail,
            'count_time'=>$companyPhysical->count_time,
            'count_time_show'=>Carbon::createFromTimestamp($companyPhysical->count_time)->toDateTimeString()
        ];

        return $rs;
    }
}
