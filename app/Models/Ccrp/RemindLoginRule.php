<?php

namespace App\Models\Ccrp;
class RemindLoginRule extends Coldchain2Model
{
    protected $table = 'task_remind_login_rule';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'remind_time', 'title', 'content', 'begin_time', 'end_time', 'status'];

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
