<?php

namespace App\Transformers\Reports;

use App\Models\Reports\CoolerLog;
use App\Transformers\CompanyTransformer;
use League\Fractal\TransformerAbstract;

class CoolerLogTransformer extends TransformerAbstract
{
    public $availableIncludes=['company'];
    private $columns = [
        'id',
        'company',
        'cooler_name',
        'cooler_sn',
        'status',
        'note',
        'name',
        'note_time'
    ];

    public function columns()
    {
        //获取字段中文名
        return CoolerLog::getFieldsTitles($this->columns);
    }

    public function transform(CoolerLog $coolerLog)
    {
        $result=[];
        foreach ($this->columns as $column)
        {
            $result[$column]=$coolerLog->{$column}??'';
        }
        return $result;
    }

    public function includeCompany(CoolerLog $coolerLog)
    {
        return $this->item($coolerLog->company,new CompanyTransformer());
    }
}
