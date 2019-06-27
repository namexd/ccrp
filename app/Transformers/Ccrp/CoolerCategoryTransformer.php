<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\CoolerCategory;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CoolerCategoryTransformer extends TransformerAbstract
{
    public function transform(CoolerCategory $category)
    {
        $arr=[
            'id'=>$category->id,
            'pid'=>$category->pid,
            'group'=>$category->group,
            'cooler_type'=>$category->cooler_type,
            'title'=>$category->title,
            'cooler_count'=>$category->cooler_count,
            'cooler_sum'=>$category->cooler_sum,
            'ctime'=>$category->ctime,
            'cuid'=>$category->cuid,
            'utime'=>$category->utime,
            'sort'=>$category->sort,
            'status'=>$category->status,
            'company_id'=>$category->company_id
        ];
        return $arr;
    }
}