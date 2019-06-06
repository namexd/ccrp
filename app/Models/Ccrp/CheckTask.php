<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;

class CheckTask extends Model
{

    protected $fillable = [
        'company_id', 'template_id', 'start','end', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function template()
    {
        return $this->belongsTo(CheckTemplate::class, 'template_id');
    }

}
