<?php

namespace App\Transformers;

use App\Models\Collector;
use App\Models\WarningEvent;
use App\Models\WarningEventOption;
use App\Models\WarningSetting;
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
