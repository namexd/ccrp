<?php

namespace App\Models\Ccrp;


use App\Http\Controllers\Api\Ccrp\Controller;
use App\Models\CoolerCategory;
use function App\Utils\http;

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

    public function category()
    {
        return $this->belongsTo(CoolerCategory::class);
    }

    public function warningSetting()
    {
        return $this->hasOne(SenderWarningSetting::class,'id','id');
    }

    public function checkWarningSetting()
    {
        $settings = self::where('status', 1)->doesntHave('warningSetting')->get();
        $i = 1;
        foreach ($settings as $setting) {
            $i++;
            dump($setting->toArray());
        }
        dd($i);

    }

    //巡检报告-启用冷链装备报警未开启清单
    public function getWarningUnableSender($company_id, $quarter = '')
    {
        $company_ids = Company::find($company_id)->ids(0);
        return $this->where(function ($query) {
            $query->whereHas('warningSetting', function ($query) {
                $query->where('status', '<>', 1)->orWhere('power_warning', '<>', 1)->orWhere('warninger_id','<=',0);
            })->orWhere(function ($query) {
                $query->whereDoesntHave('warningSetting');
            });
        })
            ->where('status', 1)
            ->whereIn('company_id', $company_ids)
            ->with(['company' => function ($query) {
                $query->selectRaw('id,title');
            }, 'warningSetting' => function ($query) {
                $query->select('sender_id');
            }])
            ->selectRaw('id,company_id,sender_id,note')
            ->get()
            ->toArray();
    }

    //巡检报告-主机断电清单
    public function getPowerOffSender($company_id, $date = '')
    {
        $company_ids = Company::find($company_id)->ids(0);
        return $this->where('ischarging', 0)
            ->where('status',1)
            ->whereIn('company_id', $company_ids)
            ->whereBetween('ischarging_update_time',$date)
            ->with(['company' => function ($query) {
                $query->selectRaw('id,title');
            }])
            ->selectRaw('company_id,sender_id,note,ischarging_update_time')
            ->get()
            ->toArray();
    }

    //获取最近一天的最新主机数据包
    public  function getRealTimeStatus($sender_id,$product_sn)
    {
          $url=env('SENDER_STATUS_URL');
          $result=http('GET',$url.'/'.$sender_id.'_'.$product_sn);
          $result = json_decode($result, true);
          return $result;

    }
    //获取最近一天的最新断电设备清单
    public  function getUncharging()
    {
        $url=env('SENDER_STATUS_URL');
        $result= http('GET',$url.'/uncharging');
        $result = json_decode($result, true);
        return $result;

    }
}
