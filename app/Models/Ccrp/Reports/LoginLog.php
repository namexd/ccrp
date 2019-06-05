<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2pgModel;
use App\Models\Ccrp\Company;
use App\Traits\ModelFields;

class LoginLog extends Coldchain2pgModel
{
    use ModelFields;
    protected $table='user_login_log';

    const LOGIN_TYPE=[
        '1'=>'电脑PC',
        '2'=>'手机',
        '3'=>'微信',
        '4'=>'APP',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    public function getDetailByDate($companyIds,$start,$end)
    {
       return $this->whereBetween('login_time',[$start,$end])->whereIn('company_id',$companyIds)->orderBy('login_time');
    }
    public function getReportByMonth($companyIds,$date)
    {
        $montharr = explode('-', $date);
        $data=[];
        $year = $montharr[0];
        $month = $montharr[1];
        $company_id=implode(',',$companyIds);
        $begin = $year . '-' . $month . '-01';
        $end = date('Y-m-d', strtotime($begin . ' +1 month -1 day'));
        $sql = 'select lf.date,lf.`day`,lf.id,lf.title,lf.tm,IFNULL(ri.login,0) as login
FROM
(
SELECT dt.date,dt.`day`,uc.id,uc.title,dt.tm
from (
select date,`day`,`month`,`year`,"AM" as tm from ck_date WHERE `year`=' . $year . ' and `month`=' . $month . '
UNION ALL
select date,`day`,`month`,`year`,"PM" as tm from ck_date WHERE `year`=' . $year . ' and `month`=' . $month . '
) as dt,ck_user_company as uc
WHERE uc.id in (' . $company_id . ')
) as lf 
LEFT JOIN
(
select DISTINCT ll.company_id,DATE_FORMAT(FROM_UNIXTIME(ll.login_time),"%Y-%m-%d") as date,FROM_UNIXTIME(ll.login_time,"%p") as tm, 1 as login
from ck_user_login_log AS ll 
where ll.company_id in (' . $company_id . ') and DATE_FORMAT(FROM_UNIXTIME(ll.login_time),"%Y-%m-%d") BETWEEN "' . $begin . '" and "' . $end . '"
) as ri ON ri.company_id=lf.id and ri.date=lf.date and ri.tm=lf.tm
ORDER BY lf.`day`,lf.id,lf.tm;';
       $result= \DB::connection('dbyingyong')->select($sql);
        foreach ($result as $vo) {
            $data[$vo->id]['title'] = $vo->title;
            $data[$vo->id][$vo->day][$vo->tm] = $vo->login;
        }
       return $data;

    }
    static public function fieldTitles()
    {
        return[
            'company'=>'单位',
            'username'=>'登录名',
            'type'=>'登录方式',
            'login_time'=>'登录时间',
            'note'=>'备注',
        ];
    }
}
