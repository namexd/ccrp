<?php

namespace App\Models\Ccrp;

class CheckTaskResult extends Coldchain2ModelWrite
{
    protected $fillable=[
        'task_id','key','value'
    ];
    public function task()
    {
        return $this->belongsTo(CheckTask::class,'task_id');
    }

    public function getValueAttribute($value)
    {
        return json_decode($value,true);
    }
}
