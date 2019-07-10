<?php

namespace App\Models\Ccrp;


use App\Models\CoolerCategory;

class Sender extends Coldchain2Model
{
    protected $table = 'sender';
    public $timestamps=false;

    protected $fillable = [
        'senderid',
        'sender_id',
        'supplier_model',
        'supplier_id',
        'category_id',
        'company_id',
        'status',
        'note',
        'note2',
        'simcard',
        'ischarging',
        'ischarging_update_time',
        'install_uid',
        'install_time',
        'uninstall_time',
        'update_time'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

   public function cooler_category()
   {
       return $this->belongsTo(CoolerCategory::class,'category_id','id');
   }
   public function dccharging()
   {
       return $this->hasOne(Dccharging::class,'sender_id','sender_id');
   }

   public function warning_setting()
   {
       return $this->hasOne(SenderWarningSetting::class,'sender_id','sender_id')->where('status',1);
   }

   public function create($attribute){
        $result=parent::create($attribute);
        return $result;
   }
}
