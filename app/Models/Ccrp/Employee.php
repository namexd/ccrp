<?php

namespace App\Models\Ccrp;

class Employee extends Coldchain2Model
{
    protected $table = 'employee';

    protected $fillable = ['username', 'password', 'category', 'note', 'userlevel', 'phone', 'menu_company', 'status'];

    const CATEGORIES = [0 => '实施人员', 1 => '巡检客服'];
    const USERLEVELS = [1 => '通用管理员', 2 => '员工'];
}
