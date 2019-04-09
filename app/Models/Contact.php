<?php
namespace App\Models;
class Contact extends Coldchain2Model
{
    protected $table = 'contact';
    protected $primaryKey = 'contact_id';

    protected $fillable = ['contact_id','name','phone','email','job','voice','note','level','company_id','create_uid','create_time','status','category_id'];

    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
