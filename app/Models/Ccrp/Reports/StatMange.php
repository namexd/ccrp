<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Company;
use App\Traits\ModelFields;

class StatMange extends Coldchain2Model
{
    use ModelFields;
    protected $table = 'stat_manage';
    public function company($year = null, $month = null)
    {
        $rs = $this->belongsTo(Company::class);

        if ($year and $month) {
            $rs = $rs->where('year', $year)->where('month', $month);
        }
        return $rs;
    }

    static public function fieldTitles()
    {
        return [
            'year' => '年',
            'month' => '月',
            'devicenum' => '设备数量',
            'totalwarnings' => '预警数量',
            'humanwarnings' => '人为造成预警次数',
            'highlevels' => '未及时处理预警',
            'unlogintimes' => '未按规定登录平台次数',
            'grade' => '冷链管理评估值'
        ];
    }
}
