<?php

namespace App\Transformers\Reports;

use App\Models\Reports\StatCooler;
use League\Fractal\TransformerAbstract;

class StatCoolerTransformer extends TransformerAbstract
{
    private $columns = [
        'id',
        'month',
        'temp_avg',
        'temp_high',
        'temp_low',
        'error_times',
        'warning_times',
        'temp_variance',
    ];

    public function columns()
    {
        //获取字段中文名
        return StatCooler::getFieldsTitles($this->columns);
    }

    public function transform(StatCooler $statCooler)
    {
        $result=[];
        foreach ($this->columns as $column)
        {
            $result[$column]=$statCooler->{$column}??'';
        }
        return $result;
    }

}
