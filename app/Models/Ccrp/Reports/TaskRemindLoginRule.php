<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;

class TaskRemindLoginRule extends Coldchain2Model
{

    protected $table = 'task_remind_login_rule';

    protected $fillable = [
        'category','name','remind_time','title','content','begin_time','end_time','status'
    ];
}
