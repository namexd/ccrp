<?php
namespace App\Models\Ccrp;
use App\Traits\ModelFields;

class CompanyPhoto extends Coldchain2ModelWithTimestamp
{
    use ModelFields;
    protected $table = 'company_photos';

    protected $fillable = ['company_id','sys_id','value'];

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    public function getValueAttribute($value)
    {
        return $value ? config('app.we_url').'/files/'.$value : '';

    }
}
