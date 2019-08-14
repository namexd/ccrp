<?php

namespace App\Models\Ccrp;

class CompanyHasTag extends Coldchain2Model
{
    protected $table = 'company_has_tags';
    protected $fillable = ['tag_id', 'company_id'];

}
