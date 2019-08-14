<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Company;
use App\Traits\ModelFields;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

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

    public function getListByMonths($company_ids,$start,$end)
    {
        $start=Carbon::createFromTimestamp(strtotime($start));
        $end=Carbon::createFromTimestamp(strtotime($end));
        $start_month=$start->firstOfMonth()->timestamp;
        $end_month=$end->endOfMonth()->timestamp;
        $result=$this->selectRaw('*,ROUND(avg(grade),2) as grade')->whereRaw('(CONVERT((UNIX_TIMESTAMP(concat(year,"-",if(length(month)=1,concat(0,month),month),"-01"))),SIGNED) between '.$start_month.' and '.$end_month.')')->whereIn('company_id',$company_ids)->groupBy('company_id');
        return $result;
    }

    public function paginate()
    {
        $perPage = Request::get('per_page', 10);

        $page = Request::get('page', 1);

        $start = ($page - 1) * $perPage;
        $start_time = Request::get('start');
        $end_etime = Request::get('end');
        $company_id = Request::get('company_id');
        $company_ids = Company::find($company_id)->ids();
        $start = Carbon::createFromTimestamp(strtotime($start_time));
        $end = Carbon::createFromTimestamp(strtotime($end_etime));
        $start_month = $start->firstOfMonth()->timestamp;
        $end_month = $end->endOfMonth()->timestamp;
        $result = $this->selectRaw('company_id,MAX(devicenum) as devicenum,sum(humanwarnings) as humanwarnings,sum(highlevels) as highlevels,sum(totalwarnings) as totalwarnings,sum(unlogintimes) as unlogintimes,ROUND(avg(grade),2) as grade')->whereRaw('(CONVERT((UNIX_TIMESTAMP(concat(year,"-",if(length(month)=1,concat(0,month),month),"-01"))),SIGNED) between '.$start_month.' and '.$end_month.')')->whereIn('company_id', $company_ids)->groupBy('company_id')->with(['company'])->get()->toArray();

        $movies = static::hydrate($result);

        $paginator = new LengthAwarePaginator($movies, count($result), $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }
}
