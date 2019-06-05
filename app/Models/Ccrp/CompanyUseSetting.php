<?php

namespace App\Models\Ccrp;

class CompanyUseSetting extends Coldchain2ModelWithTimestamp
{
    protected $table = 'company_use_settings';
    protected $primaryKey = 'id';
    protected $fillable = ['setting_id', 'company_id', 'value'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
