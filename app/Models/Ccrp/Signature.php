<?php

namespace App\Models\Ccrp;
class Signature extends Coldchain2Model
{
    protected $table = 'signature';
    protected $primaryKey = 'id';

    protected $fillable = ['deliverorder', 'signature', 'sign_time', 'company_id', 'img_name', 'file_uniqid'];

    function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
