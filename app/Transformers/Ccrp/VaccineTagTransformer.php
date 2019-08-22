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
        //临时演示作用 2019年08月21日 Liu
        if(request()->route()->getAction()['as']=='api.ccrp.coolers.show' and $tags->pivot and $tags->pivot->inventory_quantity)
        {
            $arr['name'] .= '【'.$tags->pivot->inventory_quantity.'】';
        }
        return $arr;
    }
}
