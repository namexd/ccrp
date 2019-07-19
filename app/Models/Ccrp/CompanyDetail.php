<?php
namespace App\Models\Ccrp;
use App\Traits\ModelFields;

class CompanyDetail extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'company_details';

    protected $fillable = ['company_id','sys_id','value'];

    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

}
