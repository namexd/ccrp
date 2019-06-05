<?php

namespace App\Transformers\Ccrp\Reports;

use App\Models\Ccrp\Reports\StatMange;
use App\Transformers\Ccrp\CompanyTransformer;
use League\Fractal\TransformerAbstract;

class StatManageTransformer extends TransformerAbstract
{
    public $availableIncludes=['company'];
    private $columns = [
        'id',
        'year',
        'month',
        'devicenum',
        'totalwarnings',
        'humanwarnings',
        'highlevels',
        'unlogintimes',
        'grade'
    ];

    public function columns()
    {
        //获取字段中文名
        return StatMange::getFieldsTitles($this->columns);
    }

    public function transform(StatMange $statMange)
    {
        $result=[];
        foreach ($this->columns as $column)
        {
            $result[$column]=$statMange->{$column}??'';
        }
        return $result;
    }

    public function includeCompany(StatMange $statMange)
    {
        return $this->item($statMange->company,new CompanyTransformer());
    }
}