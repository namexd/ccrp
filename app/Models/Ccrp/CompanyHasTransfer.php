<?php

namespace App\Models\Ccrp;

class CompanyHasTransfer extends Coldchain2Model
{
    protected $table = 'company_has_transfers';
    protected $fillable = ['transfer_id', 'company_id'];

}
