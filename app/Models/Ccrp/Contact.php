<?php
namespace App\Models\Ccrp;
use App\Traits\ModelFields;

class Contact extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'contact';
    protected $primaryKey = 'contact_id';

    protected $fillable = ['contact_id','name','phone','email','job','voice','note','level','company_id','create_uid','create_time','status','category_id'];

    function company()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }


    protected static function fieldTitles()
    {
        return [
            'name' => '姓名',
            'phone' => '电话',
            'note' => '备注',
        ];
    }
}
