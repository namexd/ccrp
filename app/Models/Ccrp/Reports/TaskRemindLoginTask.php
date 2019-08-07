<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Company;

class TaskRemindLoginTask extends Coldchain2Model
{

    protected $table = 'task_remind_login_task';

    protected $fillable = [
        'company_id','rule_id','remind_time','remind_date','wxcode','title','content'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
    public function rule()
    {
        return $this->belongsTo(TaskRemindLoginRule::class,'rule_id');
    }
}
