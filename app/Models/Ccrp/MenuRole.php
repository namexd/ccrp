<?php

namespace App\Models\Ccrp;


class MenuRole extends Coldchain2Model
{
    protected $table = 'menu_role';

    public function setRoleAttribute()
    {
        if ($this->role_id==1)
        {
            $this->attributes['role']='cdc';
        }
        if ($this->role_id==2)
        {
            $this->attributes['role']='unit';
        }
    }
}
