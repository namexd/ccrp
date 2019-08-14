<?php

namespace App\Models\Ccrp;
class CompanyHasRemindLogin extends Coldchain2Model
{
    protected $table = 'task_remind_login_company';

    protected $fillable = ['rule_id', 'company_id'];

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    function rule()
    {
        return $this->belongsTo(RemindLoginRule::class, 'rule_id', 'id');
    }
}
