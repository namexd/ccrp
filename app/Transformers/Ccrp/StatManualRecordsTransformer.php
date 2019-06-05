<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Reports\StatManualRecord;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class StatManualRecordsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['signature'];

    public function transform(StatManualRecord $row)
    {
        return ["id" => $row->id,
            "company_id" => $row->company_id,
            "year" => $row->year,
            "month" => $row->month,
            "day" => $row->day,
            "cooler_id" => $row->cooler_id,
            "cooler_name" => $row->cooler_name,
            "cooler_sn" => $row->cooler_sn,
            "cooler_type" => $row->cooler_type,
            "temp_cool" => $row->temp_cool,
            "temp_cold" => $row->temp_cold,
            "sign_note" => $row->sign_note,
            "sign_id" => $row->sign_id,
            "sign_time" => $row->sign_time ? Carbon::createFromTimestamp($row->sign_time)->toDateTimeString() : '',
            "sign_time_a" => $row->sign_time_a,
            "create_time" => $row->create_time ? Carbon::createFromTimestamp($row->create_time)->toDateTimeString() : '',
        ];

    }

    public function includeSignature(StatManualRecord $row)
    {
        if ($row->signature) {
            return $this->item($row->signature, new SignatureTransformer());
        } else {
            return null;
        }
    }


}