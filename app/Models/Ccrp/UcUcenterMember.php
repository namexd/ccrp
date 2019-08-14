<?php

namespace App\Models\Ccrp;

class UcUcenterMember extends Coldchain2ucenterModel
{
    protected $table = 'ucenter_member';
    protected $primaryKey = 'id';

    public function addNew($user)
    {
        //id 用户ID	username 用户名	password 密码	email 用户邮箱	mobile 用户手机	reg_time 注册时间	reg_ip 注册IP	last_login_time 最后登录时间	last_login_ip 最后登录IP	update_time 更新时间	status 用户状态	type 1为用户名注册，2为邮箱注册，3为手机注册

        $this->username = $user->username;
        $this->password = $user->password;
        $this->mobile = '';
        $this->reg_time = time();
        $this->reg_ip = 0;
        $this->status = 1;
        $this->type = 1;
        $this->email = '';
        $this->save();
        return $this;
    }

}
