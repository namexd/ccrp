<?php

namespace App\Models\Ccrp;

class Tag extends Coldchain2Model
{
    protected $table = 'tags';
    protected $fillable = ['name', 'type', 'slug'];

    const TYPE = [
        'company' => '单位标记',

    ];
    const 自定义设置 = 5;
    const 管理单位 = 'manager';

    function companies()
    {
        return $this->hasManyThrough(Company::class, CompanyHasTag::class);
    }

    public function getUpdatedAtColumn()
    {
        return parent::UPDATED_AT;
    }

    public function getCreatedAtColumn()
    {
        return parent::CREATED_AT;
    }

    public function setUpdatedAt($value)
    {
        return parent::UPDATED_AT;
    }

    public function setCreatedAt($value)
    {
        return parent::CREATED_AT;
    }


}
