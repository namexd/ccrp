<?php

namespace App\Models;

use function App\Utils\app_access_encode;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    const 冷链监测系统 = 'ccrp';
    const 疫苗追溯系统 = 'bpms';
    protected $fillable = [
        'name',
        'appkey',
        'appsecret',
        'slug',
        'image',
        'note',
        'status',
    ];
}
