<?php

namespace App\Models\Ccrp;

class UcMember extends Coldchain2ucenterModel
{
    protected $table = 'member';
    protected $primaryKey = 'uid';

    public function addNew($user)
    {
        $this->nickname = $user->username;
        $this->sex = 0;
        $this->status = 1;
        $this->reg_time = time();
        $this->app_id = 2;
        $this->last_login_role = 0;
        $this->show_role = 0;
        $this->signature = '';
        $this->pos_province = 0;
        $this->pos_city = 0;
        $this->pos_district = 0;
        $this->pos_community = 0;
        $this->qq = '';
        $this->save();
        return $this;
    }
}
