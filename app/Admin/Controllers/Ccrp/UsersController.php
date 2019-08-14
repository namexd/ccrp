<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Actions\User\AdminLogin;
use App\Admin\Extensions\ExcelExpoter;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\User;
use Encore\Admin\Controllers\AdminController;
use function app\Utils\loginkey;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Redirect;

class UsersController extends AdminController
{
    use HasResourceActions;

    protected $title = '登录账号';

    public function login(Company $company)
    {
        $user = User::where('company_id', $company->id)->first();
        if ($user->binding_domain) {
            $domain = Company::ONLINE_DOMAIN;
            $domain_pre = 'http://' . $domain[$user->binding_domain] . '/';
        } else {
            $domain_pre = 'http://www2.coldyun.com/';
        }
        $url = $domain_pre . '/user/admin_login?cdc_admin=1&key=' . loginkey() . '&id=' . $user['id'];
        return redirect()->away($url);
    }

    public function loginCcrps(Company $company)
    {
        $user = User::where('company_id', $company->id)->first();
        $domain_pre = 'https://ccrps.coldyun.net/';
        $url = $domain_pre . '/user/admin_login?cdc_admin=1&key=' . loginkey() . '&id=' . $user['id'];
        return redirect()->away($url);
    }

    public function loginWechat(Company $company)
    {
        $user = User::where('company_id', $company->id)->orderBy('id','asc')->first();
        $domain_pre = 'http://weixin.coldyun.com/';
        $url = $domain_pre . '/user/admin_login?cdc_admin=1&key=' . loginkey() . '&id=' . $user['id'];
        return redirect()->away($url);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->model()->orderBy('id', 'desc');
        $grid->id('Id');
        $grid->userlevel('角色');
        $grid->binding_domain('绑定域名');
        $grid->username('登录名');
        $grid->company('单位名称');
        $grid->company_id('单位ID');
        $grid->company_type('单位类型')->using(Company::COMPANY_TYPE);
        $grid->email('邮箱');
        $grid->mobile('手机');
        $grid->realname('姓名');
        $grid->login('登录次数');
        $grid->last_login_time('上次登录时间')->display(function ($value) {
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->last_login_ip('上次登录ip');
        $grid->reg_ip('注册ip');
        $grid->ctime('注册时间')->display(function ($value) {
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->status('状态')->using(User::STATUSES);;

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->add(new AdminLogin());
        });


//        $grid->exporter($this->excel());

        $grid->filter(function ($filter) {
            $filter->like('company', '单位名称');
            $filter->like('username', '登录名');
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('usertype', __('Usertype'));
        $show->userlevel('Userlevel');
        $show->binding_domain('Binding domain');
        $show->username('Username');
        $show->company('Company');
        $show->company_id('Company id');
        $show->company_type('Company type');
        $show->email('Email');
        $show->mobile('Mobile');
        $show->group('Group');
        $show->vip('Vip');
        $show->avatar('Avatar');
        $show->score('Score');
        $show->money('Money');
        $show->sex('Sex');
        $show->age('Age');
        $show->birthday('Birthday');
        $show->summary('Summary');
        $show->realname('Realname');
        $show->idcard_no('Idcard no');
        $show->login('Login');
        $show->last_login_time('Last login time');
        $show->last_login_ip('Last login ip');
        $show->reg_ip('Reg ip');
        $show->reg_type('Reg type');
        $show->ctime('Ctime');
        $show->utime('Utime');
        $show->sort('Sort');
        $show->status('Status');
        $show->cooler_category('Cooler category');
        $show->binding_vehicle('Binding vehicle');
        $show->binding_printer('Binding printer');
        $show->menu_setting('Menu setting');
        $show->to_uc_time('To uc time');
        $show->uc_user_id('Uc user id');

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });;
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);
        $form->text('username', 'Username');
        $form->email('email', 'Email');
        $form->mobile('mobile', 'Mobile');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
//
//    public function excel()
//    {
//
//        $excel = new ExcelExpoter();
//        $excel->setFileName('登录账号');
//        $columns = [
//            'id' => 'ID',
//            'userlevel' => 'Userlevel',
//            'binding_domain' => '绑定域名',
//            'username' => '登录名',
//            'company' => '单位名称',
//            'company_type' => '单位类型',
//            'email' => '邮箱',
//            'mobile' => '手机',
//            'realname' => '姓名',
//            'login' => '登录次数',
//        ];
//        $excel->setColumn($columns);
//        return $excel;
//    }
}
