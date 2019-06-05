<?php

namespace App\Models\Ccrp;

use App\Models\Ccrp\Reports\StatManualRecord;

class CompanyHasFunction extends Coldchain2Model
{



    const 签名间隔小时 = 6;
    const 签名上午时间 = [6, 7, 8, 9, 10, 11];
    const 签名下午时间 = [12, 13, 14, 15, 16, 17, 18];
    const 签名设备类型 =
        [
            Cooler::设备类型_冷藏冰箱,
            Cooler::设备类型_冷冻冰箱,
            Cooler::设备类型_普通冰箱,
            Cooler::设备类型_冷藏冷库,
            Cooler::设备类型_冷冻冷库
        ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function function ()
    {
        return $this->belongsTo(CompanyFunction::class, 'id', 'function_id');
    }

    /**
     * 需要人工测温
     * @param bool $with_tips
     * @return array|bool
     */
    public function needManualRecord($with_tips = false)
    {
        $result = ['status' => true, 'tips' => '疫苗储存和运输管理规范（2017年版），第十一条规定："每天上午和下午至少各进行一次人工温度记录（间隔不少于6小时）"； 建议上午8~9:00，下午16~17:00进行记录'];
        if ((bool)$this->hasManualRecords()->count()) {
            $result = ['status' => false, 'tips' => '已经签过了'];
        } else {
            if (date('A') == 'AM') {
                if (!in_array(date('H'), self::签名上午时间)) {
                    $result = ['status' => false, 'tips' => '请在' . current(self::签名上午时间) . '点~' . last(self::签名上午时间) . '点时间记录'];
                }
            }
            if (date('A') == 'PM') {
                if (!in_array(date('H'), self::签名下午时间)) {
                    $result = ['status' => false, 'tips' => '请在' . current(self::签名下午时间) . '点~' . last(self::签名下午时间) . '点时间记录'];
                } else {
                    $am_record = $this->hasManualRecords(null, null, null, 'AM')->first();
                    if ($am_record and time() - $am_record->sign_time < (self::签名间隔小时 * 3600)) {
                        $result = ['status' => false, 'tips' => '距离上午记录时间（' . date('H:i', $am_record->sign_time) . '）还没有超过6小时，预计' . (date('H:i', $am_record->sign_time + 6 * 3600) . '可以记录')];
                    }
                }
            }
        }
        if ($with_tips) {
            return $result;
        } else {
            return (bool)$result['status'];
        }
    }

    //记录关系表
    public function manaualRecords()
    {
        return $this->hasMany(StatManualRecord::class, 'company_id', 'company_id');
    }

    //指定时间查询
    public function hasManualRecords($year = null, $month = null, $day = null, $session = null)
    {
        return $this->manaualRecords
            ->where('year', $year ?? date('Y'))
            ->where('month', $month ?? date('m'))
            ->where('day', $day ?? date('d'))
            ->where('sign_time_a', $session ?? date('A'));
    }


}
