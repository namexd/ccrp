<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningEventOption;
use App\Models\Ccrp\WarningSetting;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningEventOptionTransformer extends TransformerAbstract
{

    public function transform(WarningEventOption $option)
    {
        $result = [
            'id' => $option->id,
            'warning_type' => $option->warning_type,
            'title' => $option->title,
        ];
        return $result;
    }

}