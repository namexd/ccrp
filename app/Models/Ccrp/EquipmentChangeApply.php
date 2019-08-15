<?php

namespace App\Models\Ccrp;

use App\Models\Upload;
use App\Models\User;
use Carbon\Carbon;
use DB;
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
        'check_commnet',
        'check_time',
        'handler',
        'end_time',
        'comment',
        'status',
        'is_auto'
    ];
    const 冷链设备关闭报警 = 1;
    const 冷链设备开通报警 = 2;
    const 冰箱参数修改 = 3;
    const 冰箱备用 = 4;
    const 冰箱报废 = 5;
    const 报警联系人变更 = 6;
    const 冰箱更换_报废_备用 = 7;
    const 冰箱启用 = 8;
    const 改温度区间 = 9;
    const 取消探头 = 10;
    const 新增冰箱 = 11;
    const 门诊注销_停止监测 = 12;
    const 报警延迟时间修改 = 13;

    const CHANGE_TYPE = [
        self::冷链设备关闭报警 => '冷链设备关闭报警',
        self::冷链设备开通报警 => '冷链设备开通报警',
        self::冰箱参数修改 => '冰箱参数修改',
        self::冰箱备用 => '冰箱备用',
        self::冰箱报废 => '冰箱报废',
        self::报警联系人变更 => '报警联系人变更',
        self::冰箱更换_报废_备用 => '冰箱更换(报废 / 备用)',
        self::冰箱启用 => '冰箱启用',
        self::改温度区间 => '改温度区间',
        self::取消探头 => '取消探头',
        self::新增冰箱 => '新增冰箱',
        self::门诊注销_停止监测 => '门诊注销，停止监测',
        self::报警延迟时间修改 => '报警延迟时间修改'

    ];
    const 状态_待审核 = 0;
    const 状态_未处理 = 1;
    const 状态_处理完成 = 2;
    const 状态_审核未通过 = 3;
    const STATUS = [
        self::状态_待审核 => '待审核',
        self::状态_未处理 => '未处理',
        self::状态_处理完成 => '处理完成',
        self::状态_审核未通过 => '审核未通过',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function details()
    {
        return $this->hasMany(EquipmentChangeDetail::class, 'apply_id');
    }

    public function news()
    {
        return $this->hasMany(EquipmentChangeNew::class, 'apply_id');
    }

    public function contact()
    {
        return $this->hasOne(EquipmentChangeContact::class, 'apply_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'handler');
    }

    public function checkUnit()
    {
        return $this->belongsTo(Company::class, 'check_unit');
    }

    public function checkUser()
    {
        return $this->belongsTo(\App\Models\Ccrp\User::class, 'check_user');
    }

    public function add($data)
    {
        try {
            $apply = DB::transaction(function () use ($data) {
                $attributes = array_only($data, $this->fillable);
                $attributes['apply_time'] = Carbon::now()->toDateTimeString();
                if ($apply = self::create($attributes)) {
                    if (array_has($data, 'details')) {
                        $details = json_decode($data['details'], true);

                        if (is_array($details) && !is_null($details)) {
                            $apply->details()->createMany($details);
                        }
                    }

                    if (array_has($data, 'news')) {
                        $news = json_decode($data['news'], true);

                        if (is_array($news) && !is_null($news) && array_get(array_first($news), 'cooler_name')) {
                            $apply->news()->createMany($news);
                        }
                    }

                    if (array_has($data, 'contact')) {
                        $contact = json_decode($data['contact'], true);
                        if (is_array($contact) && !is_null($contact)) {
                            $apply->contact()->create($contact);
                        }
                    }


                    return $apply;
                }

            }, 5);
            return $apply;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }

    public function getChangeType()
    {
        $k = 0;
        foreach (self::CHANGE_TYPE as $key => $value) {
            if ($key!==self::改温度区间)
            {
                $result[$k]['key'] = $key;
                $result[$k]['value'] = $value;
                $k++;
            }
        }
        return $result;
    }

    public function getStatistics($company_ids)
    {
        $prev_date = Carbon::now()->subYear(1)->toDateTimeString();
        $now_date = Carbon::now()->toDateTimeString();
        $total = $this->selectRaw('
        count(1) as count,
         ifnull(sum(if(status=0,1,0)),0) as unhandled,
         ifnull(sum(if(status=1,1,0)),0) as handling,
         ifnull(sum(if(status=2,1,0)),0) as handled
        ')->whereIn('company_id', $company_ids)->first();
        $month_counter = $this->selectRaw('
        DATE_FORMAT(apply_time,"%Y-%m") as month,
        count(1) as month_counter
        ')->whereBetween('apply_time', [$prev_date, $now_date])
            ->whereIn('company_id', $company_ids)
            ->groupBY(DB::raw(' DATE_FORMAT(apply_time,"%Y-%m")'))
            ->get();
        $today = $this->selectRaw('
        count(1) as count,
        ifnull(sum(if(status=0,1,0)),0) as unhandled,
        ifnull(sum(if(status=1,1,0)),0) as handling,
        ifnull(sum(if(status=2,1,0)),0) as handled
        ')->whereDate('apply_time', $now_date)
            ->whereIn('company_id', $company_ids)->first();
        return compact('total', 'today', 'month_counter');
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
