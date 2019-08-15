<?php

namespace App\Transformers\Ccrp\Reports;

use App\Models\Ccrp\Reports\StatCooler;
use App\Transformers\Ccrp\CoolerTransformer;
use League\Fractal\TransformerAbstract;

class StatCoolerMonthsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['cooler'];

    public function transform(StatCooler $statCooler)
    {
        $wdx_score = $this->wdx_score($statCooler->temp_variance);
        $avg_score = $this->avg_score($statCooler->temp_type, $statCooler->temp_avg);
        $temp_high_score = $this->temp_high_score($statCooler->temp_type, $statCooler->temp_high);
        $temp_low_score = $this->temp_low_score($statCooler->temp_type, $statCooler->temp_low);
        $result = [
            'id' => $statCooler->id,
            'temp_type' => $statCooler->temp_type == 1 ? '冷藏' : '冷冻',
            'temp_avg' => $statCooler->temp_avg,
            'temp_high' => $statCooler->temp_high,
            'temp_low' => $statCooler->temp_low,
            'error_times' => $statCooler->error_times,
            'warning_times' => $statCooler->warning_times,
            'temp_variance' => $statCooler->temp_variance,
            'wdx_score' => $wdx_score,
            'avg_score' => $avg_score,
            'temp_high_score' => $temp_high_score,
            'temp_low_score' => $temp_low_score,
            'total_score' => $wdx_score + $avg_score + $temp_high_score + $temp_low_score,
        ];

        return $result;
    }

    public function includeCooler(StatCooler $statCooler)
    {
        return $this->item($statCooler->cooler, new CoolerTransformer());
    }


    public function wdx_score($score)
    {
        if ($score <= 0.5 && $score > 0) {
            return 2;
        }
        if ($score <= 1 && $score > 0.5) {
            return 1;
        }
        if ($score <= 2 && $score > 1) {
            return 0;
        }
        if ($score > 2) {
            return -1;
        }
    }

    public function avg_score($temp_type, $score)
    {
        switch ($temp_type) {
            case '1':
                if ($score >= 2 && $score < 8)
                    return 2;
                else return 0;
                break;
            case '2':
                if ($score < -20)
                    return 2;
                if ($score >= -20 && $score < -15)
                    return 1;
                if ($score > -15)
                    return 0;
                break;
            default:
                return 0;
                break;

        }
    }

    public function temp_high_score($temp_type, $score)
    {
        switch ($temp_type) {
            case '1':
                if ($score > 16)
                    return 0;
                if ($score >= 12 && $score < 16)
                    return 1;
                if ($score >= 8 && $score < 12)
                    return 1.5;
                if ($score >= 2 && $score < 8)
                    return 1;
                if ($score < 2)
                    return -10;
                break;
            case '2':
                if ($score < -20)
                    return 2;
                if ($score >= -20 && $score < -15)
                    return 1;
                if ($score >= -15 && $score < -5)
                    return 0;
                if ($score > -5)
                    return -10;
                break;
            default:
                return 0;
                break;

        }
    }

    public function temp_low_score($temp_type, $score)
    {
        switch ($temp_type) {
            case '1':
                if ($score < 0)
                    return -1;
                if ($score >= 0 && $score < 1)
                    return 0;
                if ($score >= 1 && $score < 2)
                    return 1;
                if ($score >= 2 && $score < 8)
                    return 2;
                if ($score > 8)
                    return -10;
                break;
            case '2':
                if ($score < -20)
                    return 2;
                if ($score >= -20 && $score < -15)
                    return 1;
                if ($score >= -15 && $score < -5)
                    return 0;
                if ($score > -5)
                    return -10;
                break;
            default:
                return 0;
                break;

        }
    }
}