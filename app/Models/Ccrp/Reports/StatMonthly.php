<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Company;

class StatMonthly extends Coldchain2Model
{
    protected $table = 'stat_monthly';
    public function company($year = null, $month = null)
    {
        $rs = $this->belongsTo(Company::class);

        if ($year and $month) {
            $rs = $rs->where('year', $year)->where('month', $month);
        }
        return $rs;
    }


}
