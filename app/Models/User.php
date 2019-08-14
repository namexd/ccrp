<?php

namespace App\Models;

use App\Traits\ModelFields;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use ModelFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','phone_verified','realname'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static function columnsFields()
    {
        return [
            'name',
            'phone',
            'phone_verified',
            'realname',
            'bind_apps',
            'region',
            'created_at'
        ];
    }

    protected static function fieldTitles()
    {
        return [
            'name' => '名称',
            'phone' => '手机',
            'phone_verified'=> '手机验证',
            'realname'=> '真实姓名',
            'bind_apps'=> '绑定应用',
            'region'=> '地区',
        ];
    }
    public function hasApps()
    {
        return $this->hasMany(UserHasApp::class);
    }
    public function weuser()
    {
        return $this->hasOne(Weuser::class);
    }
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_users', 'user_id', 'role_id');
    }

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions', 'user_id', 'permission_id');
    }

    public function apps()
    {
        return $this->belongsToMany(App::class, 'user_has_apps', 'user_id', 'app_id');
    }
}
