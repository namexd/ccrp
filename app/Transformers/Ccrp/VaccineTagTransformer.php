<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\VaccineTags;
use App\Models\CoolerCategory;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class VaccineTagTransformer extends TransformerAbstract
{
    public function transform(VaccineTags $tags)
    {
        $arr=[
            'id'=>$tags->id,
            'name'=>$tags->name,
            'full_name'=>$tags->full_name,
            'code'=>$tags->code,
            'size'=>$tags->size,
            'factory'=>$tags->factory,
            'category'=>$tags->category,
            'ctime'=>$tags->ctime,
            'status'=>$tags->status,
        ];
        return $arr;
    }
}