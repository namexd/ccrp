<?php

namespace App\Transformers\Ccrp\Reports;

use App\Models\Ccrp\Company;
use League\Fractal\TransformerAbstract;

class DevicesStatisticTransformer extends TransformerAbstract
{
    private $columns = ['id',
        'title',
        'shebei_install',
        'shebei_actived',
        'shebei_install_type1',
        'shebei_install_type2',
        'shebei_install_type3',
        'shebei_install_type4',
        'shebei_install_type5',
        'shebei_install_type6',
        'shebei_install_type8',
        'shebei_install_type9',
        'shebei_install_type10',
        'shebei_install_type11',
        'shebei_install_type12',
        'shebei_install_type100',
        'shebei_install_type101',
        'shebei_actived_type1',
        'shebei_actived_type2',
        'shebei_actived_type3',
        'shebei_actived_type4',
        'shebei_actived_type5',
        'shebei_actived_type6',
        'shebei_actived_type8',
        'shebei_actived_type9',
        'shebei_actived_type10',
        'shebei_actived_type11',
        'shebei_actived_type12',
        'shebei_actived_type100',
        'shebei_actived_type101',
        ];

    public function columns()
    {
        //获取字段中文名
        return Company::getFieldsTitles($this->columns);
    }

    public function transform(Company $company)
    {
        $result = [];
        foreach ($this->columns as $column) {
            $result[$column] = $company->{$column} ?? '';
        }
        return $result;
    }
}