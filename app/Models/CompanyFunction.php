<?php

namespace App\Models;


class CompanyFunction extends Coldchain2Model
{

    const 人工签名ID = 3;

    public function hasFunction()
    {
        return $this->hasMany(CompanyFunction::class, 'function_id', 'id');
    }

}
