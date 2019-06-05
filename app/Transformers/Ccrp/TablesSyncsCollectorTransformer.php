<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class TablesSyncsCollectorTransformer extends TransformerAbstract
{
    public function transform(Collector $collector)
    {
        return $collector->toArray();

    }
}