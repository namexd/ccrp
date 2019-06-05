<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Models\App;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Contact;
use App\Models\Ccrp\Warninger;
use App\Models\User;
use App\Transformers\Ccrp\ContactTransformer;
use App\Transformers\Ccrp\Reports\CompanySettingsTransformer;
use App\Transformers\Ccrp\WarningerTransformer;
use App\Transformers\UserHasAppTransformer;
use App\Transformers\UserTransformer;
use function App\Utils\hidePhone;
use Route;

class CompaniesController extends Controller
{
    public function infomation()
    {
        $info['data'][] = [
            "title" => '单位名称、地址、负责人、电话等信息',
            'meta' => [
                "header" => '单位信息',
                "detail_data" => '/api/ccrp/reports/companies/infomation/company?with=columns',
            ]
        ];
        $info['data'][] = [
            "title" => '一、二、三级超温预警短信接收人员',
            'meta' => [
                "header" => '预警通道设置',
                "detail_data" => '/api/ccrp/reports/companies/infomation/warningers?with=columns',
                "detail_template"=>'list'
            ]
        ];
        $info['data'][] = [
            "title" => '可以绑定系统的人员',
            'meta' => [
                "header" => '单位联系人',
                "detail_data" => '/api/ccrp/reports/companies/infomation/concats',
                "detail_template"=>'list'
            ]
        ];
        $info['data'][] = [
            "title" => '已经绑定系统的人员',
            'meta' => [
                "header" => '小程序绑定人员',
                "detail_data" => '/api/ccrp/reports/companies/infomation/users?with=columns',
                "detail_template"=>'list'
            ]
        ];
        $info["meta"]["columns"] = [
            [
                "label" => "",
                "value" => "title"
            ]
        ];
        return $this->response->array($info);
    }

    public function infomationDetail($slug)
    {
        $this->check();
        switch ($slug) {
            case 'company':
                $this->setCrudModel(Company::class);
                $return = $this->response->item($this->company, new CompanySettingsTransformer());
                return $this->display($return,'columns');
                break;
            case 'warningers':
                $this->setCrudModel(Warninger::class);
                $warningers = Warninger::where('company_id',$this->company->id)->get();
                return $this->display($this->response->collection($warningers,new WarningerTransformer()),'columns');
            case 'concats':
                $this->setCrudModel(Contact::class);
                $users =Contact::where('company_id', $this->company->id)->get();
                return $this->display($this->response->collection($users,new ContactTransformer()),'columns');
                break;
            case 'users':
                $this->setCrudModel(User::class);
                $app = App::where('slug',App::冷链监测系统)->first();
                $users =User::whereIn('id', $app->hasUser->where('app_id',$app->id)->where('app_unitid',$this->company->id)->pluck('user_id'))->get();
                return $this->display($this->response->collection($users,new UserTransformer()),'columns');
                break;

        }
    }
}
