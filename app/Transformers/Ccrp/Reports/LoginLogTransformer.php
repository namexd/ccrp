<?php

namespace App\Transformers\Ccrp\Reports;

use App\Models\Ccrp\Reports\LoginLog;
use App\Transformers\Ccrp\CompanyTransformer;
use League\Fractal\TransformerAbstract;

class LoginLogTransformer extends TransformerAbstract
{
    public $availableIncludes=['company'];
    private $columns = [
        'id',
        'username',
        'company',
        'type',
        'login_time',
        'note',
    ];

    public function columns()
    {
        //获取字段中文名
        return LoginLog::getFieldsTitles($this->columns);
    }

    public function transform(LoginLog $loginLog)
    {
        $result=[
            'id'=>$loginLog->id,
            'username'=>$loginLog->username,
            'company'=>$loginLog->company->title,
            'type'=>$loginLog::LOGIN_TYPE[$loginLog->type],
            'login_time'=>date('Y-m-d H:i:s',$loginLog->login_time),
            'note'=>$loginLog->note,
        ];
        return $result;
    }

    public function includeCompany(LoginLog $loginLog)
    {
        return $this->item($loginLog->company,new CompanyTransformer());
    }
}