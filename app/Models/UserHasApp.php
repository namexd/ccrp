<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasApp extends Model
{
    protected $fillable = [
        'app_id',
        'user_id',
        'app_username',
        'app_userid',
        'app_unitid'
    ];
    public function app()
    {
        return $this->belongsTo(App::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
