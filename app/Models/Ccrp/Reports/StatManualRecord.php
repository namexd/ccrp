<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Signature;
use Carbon\Carbon;
use DB;

class StatManualRecord extends Coldchain2Model
{
    protected $table = 'stat_monthly';

    public function signature()
    {
        return $this->hasOne(Signature::class, 'id', 'sign_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cooler()
    {
        return $this->belongsTo(Cooler::class);
    }

    public static function getListByMonth($company_id, $month = null)
    {
        if ($month == null) {
            $year = date('Y');
            $month = date('m');
        } else {
            $date = explode('-', $month);
            $year = $date[0];
            $month = str_pad($date[1], 2, "0", STR_PAD_LEFT);
        }
        $reports = self::where('company_id', $company_id)->where('year', $year)->where('month', $month)->select("year", "month", "day", "sign_time_a", "company_id", DB::raw("count(1) as cnt"))->groupBy('company_id', 'year', 'month', 'day', 'sign_time_a')->get();

        $data = array();
        if (!$reports) {
            return false;
        } else {
            $last_day = Carbon::now()->setDateTime($year, $month, 1, 0, 0, 0)->endOfMonth()->day;
            for ($i = 1; $i <= $last_day; $i++) {
                $row['year'] = $year;
                $row['month'] = $month;
                $row['day'] = str_pad($i, 2, "0", STR_PAD_LEFT);
                if (strtotime($year . '-' . $month . '-' . $row['day']) > time()) {
                    $row['AM'] = null;
                    $row['PM'] = null;
                } else {
                    $row['AM'] = 0;
                    $row['PM'] = 0;
                }
                $data[$i] = $row;
            }
            foreach ($reports as $re) {
                $data[$re->day][$re->sign_time_a] = $re->cnt;
            }
            $data = array_values($data);
        }
        return $data;

    }

    public static function getByDay($company_id, $day = null, $session = null)
    {
        if ($day == null) {
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $session = date('A');
        } else {
            $date = explode('-', $day);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
        }
        $reports = self::where('company_id', $company_id)->where('year', $year)->where('month', $month)->where('day', $day)->where('sign_time_a', $session)->get();

        return $reports;

    }
}
