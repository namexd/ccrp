<?php

namespace App\Models;

use App\Models\Ccrp\Company;
use Illuminate\Database\Eloquent\Model;

class CheckResult extends Model
{
    const CHECK_STATUS = [
        '0' => '异常',
        '1' => '正常'
    ];
    const HUMAN_CHECK_STATUS = [
        '0' => '未处理',
        '1' => '处理中',
        '2' => '已处理',
        '3' => '无法处理'
    ];
    protected $fillable = [
        'check_id',
        'object',
        'object_key',
        'object_value',
        'result',
        'check_times',
        'status',
        'human_status'
    ];


    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'object_value', 'id');
    }

}
