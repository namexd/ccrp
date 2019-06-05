<?php

namespace App\Models\Ccrp;


class CompanyFunction extends Coldchain2Model
{

    const 人工签名ID = 3;

    public function hasFunction()
    {
        return $this->hasMany(CompanyFunction::class, 'function_id', 'id');
    }

}
