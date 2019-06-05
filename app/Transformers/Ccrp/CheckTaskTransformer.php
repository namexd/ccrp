<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Certification;
use App\Models\CheckTask;
use League\Fractal\TransformerAbstract;

class CheckTaskTransformer extends TransformerAbstract
{

    public function transform(CheckTask $checkTask)
    {
        $arr = [
            'id' => $checkTask->id,
            'company'=>$checkTask->company->title,
            'template'=>$checkTask->template->title,
            'start'=>$checkTask->start,
            'end'=>$checkTask->end,
            'status'=>$checkTask->status,
            'url'=>config('api.domain').'/api/ccrp/check_tasks/'.$checkTask->id
        ];
        return $arr;
    }
    
}