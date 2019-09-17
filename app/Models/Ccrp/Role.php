<?php

namespace App\Models\Ccrp;

class Role extends Coldchain2Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'role'];

    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'menu_role', 'role_id', 'menu_id');
    }

}
