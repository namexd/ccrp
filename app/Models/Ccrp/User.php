<?php

namespace App\Models\Ccrp;

use Illuminate\Support\Facades\DB;

class User extends Coldchain2Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'usertype', 'userlevel', 'username', 'company', 'company_id', 'company_type', 'email', 'mobile', 'password', 'sex', 'age', 'birthday', 'realname', 'login', 'last_login_time', 'last_login_ip', 'reg_ip', 'reg_type', 'ctime', 'utime', 'status', 'cooler_category', 'binding_vehicle', 'binding_printer', 'menu_setting','sort','group','score','money','idcard_no'];


    const STATUSES = ['0' => '禁用', '1' => '正常'];

    public function userCompany()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->where('status', 1);
    }

    //验证用户名是否正确
    public function checkUsername($username)
    {
        return $this->where('username', $username)->where('status', 1)->count()?true:false;
    }

    //验证密码是否正确
    public function checkPassword($username, $password)
    {
        return $this->where('username', $username)->where('password',$this->user_md5($password))->where('status', 1)->select('id', 'username',  DB::raw('company_id as unitid'))->first()??false;
    }

    public function getByUsername($username)
    {
       return $this->where('username', $username)->where('status', 1)->select('id', 'username',  DB::raw('company_id as unitid'))->first();
    }

    public function user_md5($str, $auth_key = null)
    {
        if (!$auth_key) {
            $auth_key = 'PVHnDaiaS!wm>DopYhkMT:Mn^)UK]w#Kc}xr>vh-"z/#MMktgAf_NKx!%XPc*STF';
        }
        return '' === $str ? '' : md5(sha1($str) . $auth_key);
    }
    public function newPassword($password)
    {
        return $this->user_md5($password);
    }
    public function avatarImage()
    {
        return $this->hasOne(PublicUpload::class,'id','avatar');
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password']=$this->user_md5($value);
    }
    public function addFromCompany($username,$password,$company,$level_type,$binding_domain)
    {
        $this->userlevel = 13;
        $this->group = 0;
        $this->score = 0;
        $this->money = 0;
        $this->last_login_ip = '0.0.0.0';
        $this->reg_ip = '0.0.0.0';
        $this->reg_type = 'username';
        $this->ctime = time();
        $this->utime = time();
        $this->sort = 0;
        $this->status = 1;
        $this->company_id = 1;
        $this->company = $company->title;
        $this->company_id = $company->id;
        $this->username = $username;
        $this->password = $this->newPassword($password);
        $this->userlevel = $level_type;
        $this->binding_domain = $binding_domain;
        $this->save();
        return $this;
    }
}
