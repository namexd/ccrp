<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckTemplate extends Model
{
    const 月度=1;
    const 季度=2;
    const 年度=3;
    const 自定义=4;
    const CYCLE_TYPE=[
        self::月度=>'月度',
        self::季度=>'季度',
        self::年度=>'年度',
        self::自定义=>'自定义',
    ];

    public function variables()
    {
        return $this->hasMany(CheckTemplateVariable::class,'template_id');
    }
}
