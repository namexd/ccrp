<?php

namespace App\Models;

use App\Models\Ccrp\Company;
use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class EquipmentChangeApply extends Model
{
    protected $fillable = [
        'company_id',
        'phone',
        'apply_time',
        'user_id',
        'user_name',
        'user_sign',
        'check_unit',
        'check_user',
        'check_comment',
        'check_time',
        'handler',
        'end_time',
        'comment',
        'status',
        'is_auto'
    ];
    const CHANGE_TYPE = [
        1 => '冷链设备关闭报警',
        2 => '冷链设备开通报警',
        3 => '冰箱参数修改',
        4 => '冰箱备用',
        5 => '冰箱报废',
        6 => '报警联系人变更',
        7 => '冰箱更换(报废 / 备用)',
        8 => '冰箱启用',
        9 => '改温度区间',
        10 => '取消探头',
        11 => '新增冰箱',
        12 => '门诊注销，停止监测',
        13 => '报警延迟时间修改'
    ];
  const STATUS=[
      '待审核',
      '未处理',
      '处理完成',
      '审核未通过',
  ];
  const HANDLE_STATUS=[
      1=>'未处理',
      2=>'处理完成'
  ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function detail()
    {
        return $this->hasMany(EquipmentChangeDetail::class, 'apply_id');
    }

    public function new()
    {
        return $this->hasMany(EquipmentChangeNew::class, 'apply_id');
    }

    public function user()
    {
        return $this->belongsTo(Administrator::class,'handler');
    }

    public function checkUnit()
    {
        return $this->belongsTo(Company::class,'check_unit');
    }
    public function checkUser()
    {
        return $this->belongsTo(User::class,'check_user');
    }
    public function userSign()
    {
        return $this->belongsTo(Upload::class,'user_sign','uniqid');
    }

    public function markAsCheckedSuccess()
    {
        $this->status = 1;
        $this->save();
    }
    public function markAsCheckedFailed()
    {
        $this->status = 3;
        $this->save();
    }
}
