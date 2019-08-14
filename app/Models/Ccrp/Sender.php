<?php

namespace App\Models\Ccrp;


use App\Models\CoolerCategory;

class Sender extends Coldchain2Model
{
    protected $table = 'sender';
    public $timestamps=false;

    protected $fillable = [
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
    const LENGWANG_PRODUCT_MODEL = [
        'LDH500',
    ];
    const SUPPLIER_PRODUCT_MODEL = [
        'LDH500' => 'LDH500 一体机: 彩屏一体机',
        'LWZST300' => 'LWZST300 一体机: 报警器一体机',
        'LWZSR200' => 'LWZSR200 中继器: 中继器',
        'LWZST300S' => 'LWZST300S 一体机: 本地报警一体机',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

   public function cooler_category()
   {
       return $this->belongsTo(CoolerCategory::class,'category_id','id');
   }
   public function sender_status()
   {
       return $this->hasOne(SenderStatus::class,'supplier_sender_id','sender_id');
   }

   public function warning_setting()
   {
       return $this->hasOne(SenderWarningSetting::class,'sender_id','sender_id')->where('status',1);
   }

   public function create($attribute){
        $result=parent::create($attribute);
        return $result;
   }
    public function get_products()
    {
        $products = Product::where('status',1)->whereIn('product_type',[2, 3])->orderBy('sort','desc')->get();
        $data = array();
        foreach ($products as $item) {
            $data[]=['value'=>$item['supplier_product_model'],'title'=> ($item['product_type']==2?'一体机: ':'中继器: ').$item['product_model']];
        }
        return $data;

    }
}
