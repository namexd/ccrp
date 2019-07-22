<?php
namespace App\Models\Ccrp;

use App\Models\Ccrp\Sys\SysCompanyDetail;

class CompanyDetail extends Coldchain2ModelWithTimestamp
{
    protected $table = 'company_details';

    protected $fillable = ['company_id','sys_id','value'];
    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }
    public function sys_detail()
    {
        return $this->belongsTo(SysCompanyDetail::class, 'sys_id', 'id');
    }

}
