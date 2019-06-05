<?php

namespace App\Models\Ccrp;
class Report extends Coldchain2Model
{
    protected $connection = 'db_coldchain2';
    protected $tableName = 'ck_sender';

    function warning_sender_event(){
        return  $this->hasMany(WarningSenderEvent::class);
    }
}
