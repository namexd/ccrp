<?php

namespace App\Models\Ccrp;

class Transfer extends Coldchain2Model
{
    protected $table = 'transfers';
    protected $fillable = ['name', 'slug'];


    function companies()
    {
        return $this->hasManyThrough(Company::class, CompanyHasTransfer::class);
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
