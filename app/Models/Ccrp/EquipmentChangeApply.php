<?php

namespace App\Models\Ccrp;

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
    ];
    const CHANGE_TYPE = [
        1 => '短信报警关闭',
        2 => '短信报警重新开通',
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
    const STATUS = [
        '未处理',
        '处理中',
        '处理完成'
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

    public function user()
    {
        return $this->belongsTo(User::class, 'handler');
    }

    public function add($data)
    {
        $data['status'] = 0;
        try {
            $apply = DB::transaction(function () use ($data) {
                $attributes = array_only($data, $this->fillable);
                $attributes['apply_time'] = Carbon::now();
                if ($apply = self::create($attributes)) {
                    $details = json_decode($data['details'], true);
                    $news = json_decode($data['news'], true);
                    if (is_array($details) && !is_null($details)) {
                        $apply->details()->createMany($details);
                    }
                    if (is_array($news) && !is_null($news)) {
                        $apply->news()->createMany($news);
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
            $result[$k]['key'] = $key;
            $result[$k]['value'] = $value;
            $k++;
        }
        return $result;
    }

    public function getStatistics($company_ids)
    {
        $prev_date=Carbon::now()->subYear(1)->toDateTimeString();
        $now_date=Carbon::now()->toDateTimeString();
        $total = $this->selectRaw('
        count(1) as count,
        sum(if(status=0,1,0)) as unhandled,
        sum(if(status=1,1,0)) as handling,
        sum(if(status=2,1,0)) as handled
        ')->whereIn('company_id',$company_ids)->first();
        $month_counter = $this->selectRaw('
        DATE_FORMAT(apply_time,"%Y-%m") as month,
        count(1) as month_counter
        ')->whereBetween('apply_time', [$prev_date,$now_date])
            ->whereIn('company_id',$company_ids)
            ->groupBY(DB::raw(' DATE_FORMAT(apply_time,"%Y-%m")'))
            ->get();
        $today = $this->selectRaw('
        count(1) as count,
        ifnull(sum(if(status=0,1,0)),0) as unhandled,
        ifnull(sum(if(status=1,1,0)),0) as handling,
        ifnull(sum(if(status=2,1,0)),0) as handled
        ')->whereDate('apply_time',$now_date)
            ->whereIn('company_id',$company_ids)->first();
        return compact('total','today','month_counter');
    }
}
