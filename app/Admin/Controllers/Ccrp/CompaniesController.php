<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Actions\Company\AdminLogin;
use App\Admin\Actions\Company\Tool;
use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\Tools\TagCompany;
use App\Admin\Extensions\Tools\Test;
use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Area;
use App\Models\Ccrp\Company;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\CompanyFunction;
use App\Models\Ccrp\CompanyHasRemindLogin;
use App\Models\Ccrp\CompanyUseSetting;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\RemindLoginRule;
use App\Models\Ccrp\Sys\Setting;
use App\Models\Ccrp\Tag;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Hamcrest\Core\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('用户单位')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑单位')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Company);

        if(request()->title)
        {
            $grid->model()->orderBy('cdc_admin', 'desc')
            ->orderBy('cdc_admin', 'desc')
            ->orderBy('region_code', 'asc')
            ->orderBy('company_group', 'asc')
            ->orderBy('cdc_level', 'asc')
            ->orderBy('pid', 'asc')
            ->orderBy('sort', 'asc')
            ->orderBy('company_type', 'asc')
            ->orderBy('username', 'asc')
            ->orderBy('id', 'asc');
        }
        $grid->model()->orderBy('id', 'desc');

        $grid->id('Id');
        $grid->tools(function ($tools) {
           $tools->append(new TagCompany());
        });
        $grid->area()->merger_name('地区');
        $grid->tags('标记')->pluck('name')->label();
//        $grid->tags('标记')->pluck('id')->checkbox(Tag::all()->pluck('name','id'));
        $grid->title('单位名称');
        $grid->short_title('简称');
        $grid->office_title('官方名称');
        $grid->nipis_code('疾控NIPIS编码');
        $grid->ctime('注册时间')->display(function ($value) {
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->status('状态')->using(['0' => '禁用', '1' => '正常']);;
        $grid->manager('负责人');
//        $grid->phone('手机');
//        $grid->tel('电话');
//        $grid->address('地址');
        $grid->username('用户名');
        $grid->password('默认密码');
        $grid->pid('上级ID');
        $grid->cdc_admin('管理单位')->using(['0' => '否', '1' => '<b style="background-color:yellow">是</b>']);;
        $grid->coolers('冰箱')->count();
        $grid->collectors('探头')->count();
        $grid->warningers('报警通道')->count();
        $grid->contacts('联系人')->count();
        $grid->company_type('单位类型')->using(Company::COMPANY_TYPE);
        $grid->actions(function ($actions) {
            $actions->add(new AdminLogin());
            $actions->add(new Tool());



            $actions->append('<br><a target="_blank" href="' . route('ccrp.login', $actions->row->id) . '"> <i class="fa fa-laptop"></i></a>');
            $actions->append('<a target="_blank" href="' . route('ccrp.login.ccrps', $actions->row->id) . '"> <i class="fa fa-laptop"></i></a>');
            $actions->append('<a target="_blank" href="' . route('ccrp.login.wechat', $actions->row->id) . '"> <i class="fa fa-wechat"></i></a>');

            if ($actions->row->tags->contains(Tag::自定义设置)) {
                $actions->append('<br><a target="_blank" href="' . route('ccrp.settings_company', $actions->row->id) . '"> <i class="fa fa-cogs"></i></a>');
            }

            $actions->append('<br><a target="_blank" href="' . route('ccrp.company.tools', ['id'=>$actions->row->id]) . '"> <i class="fa fa-magic"></i></a>');
            if ($actions->row->cdc_admin ==0)
            {
                $actions->append('<a target="_blank"  href="' . route('equipment_change_applies.create', ['id'=>$actions->row->id]) . '"> <i class="fa fa-plus"></i></a>');
            }
        });


        $grid->filter(function ($filter) {
            $filter->expand();
            $filter->column(1/3, function ($filter) {
                $filter->equal('username', '用户名');
//            $filter->like('short_title', '单位简称');
                $filter->equal('phone', '手机');

            });
            $filter->column(1/3, function ($filter) {
                $filter->like('title', '单位名称');
                $filter->equal('pid', '上级ID');

                $filter->equal('cdc_admin', '疾控管理账号')->radio([
                    '' => '所有',
                    0 => '否',
                    1 => '是',
                ]);
            });
            $filter->column(1/3, function ($filter) {
                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            $query->where('shebei_actived', '>', 0)->where('cdc_admin', 0);
                            break;
                        case 'no':
                            $query->where('shebei_actived', '=', 0)->where('cdc_admin', 0);
                            break;
                    }
                }, '冰箱/冷库')->radio([
                    '' => '全部',
                    'no' => '没有监控',
                    'yes' => '有监控',
                ]);
                $filter->where(function ($query) {
                    switch ($this->input) {
                        case 'yes':
                            $query->whereHas('warningers');
                            break;
                        case 'no':
                            $query->doesntHave('warningers');
                            break;
                    }
                }, '报警通道')->radio([
                    '' => '全部',
                    'no' => '未设置',
                    'yes' => '已设置',
                ]);
            });


            $tags = Tag::where('type', 'company')->get();
            foreach ($tags as $tag) {
                $filter->scope($tag->id, $tag->name)->whereHas('tags', function ($query) {
                    $query->where('tag_id', request('_scope_'));
                });
            }
        });

        $grid->disableCreateButton();
//        $grid->disableRowSelector();

        $grid->exporter($this->excel());

        return $grid;
    }

    private function excel()
    {
        $excel = new ExcelExpoter();
        $excel->setFileName('用户单位');
        $columns = [
            'id' => 'ID',
            'title' => '单位名称',
            'short_title' => '单位简称',
            'office_title' => '官方名称',
            'manager' => '负责人',
            'phone' => '联系人',
            'address' => '地址',
            'username' => '用户名',
            'password' => '默认密码',
            'cdc_admin' => '疾控管理单位'
        ];
        $excel->setColumn($columns);
        $excel->setColumnFormat([
            'username' => ExcelExpoter::单元格格式字符,
            'password' => ExcelExpoter::单元格格式字符,
            'cdc_admin' => ExcelExpoter::单元格格式是否
        ]);
        return $excel;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show($company = Company::findOrFail($id));

        $show->id('Id');
        $show->title('单位名称');
        $show->short_title('单位简称');
        $show->office_title('官方名称（盖章）');
        $show->custome_code('客户码（前）');
        $show->company_type('单位分类')->using(Company::COMPANY_TYPE);
        $show->company_group('单位分组');
        $show->address('地址');
        $show->ctime('创建时间')->as(function ($var){
            return date('Y-m-d',$var);
        });
        $show->utime('更新时间')->as(function ($var){
            return date('Y-m-d',$var);
        });

        $show->status('状态')->using(['0'=>'禁用','1'=>'启用']);
        $show->closed('是否关闭')->using(['0'=>'否','1'=>'是']);

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Company);
        $form->tab('基本资料', function ($form) {
            $form->switch('status', '账户状态')->default(1);
            $form->checkbox('tags', '标签')->options(Tag::all()->pluck('name', 'id'));
            $form->select('company_type', '单位类型')->options(Company::COMPANY_TYPE);
            $form->text('title', '单位名称');
            $form->text('short_title', '单位简称');
            $form->text('office_title', '官方名称（印章）');
            $form->text('manager', '负责人');
            $form->email('email', '邮箱');
            $form->mobile('phone', '手机');
            $form->text('tel', '电话');
            $form->text('address', '地址');
            $form->text('username', '登录名')->readOnly();
            $form->text('password', '默认密码');
        })->tab('地区相关', function ($form) {
            $form->text('map_title', '地图标题');
            $form->text('region_code', '地区编码');
            $form->number('map_level', '地图级别')->default(10);
            $form->text('address_lat', '地图坐标纬度lat');
            $form->text('address_lon', '地图坐标经度lon');
            $form->number('company_group', '客户分类(0无分类,1cdc，2动物疫控,3血液,4动物农场)');
            $form->switch('list_not_show', '分类树中隐藏');
            $form->number('pid', '上级id');
//         $form->switch('address_xy_check', '是否自动校准坐标')->default(-1);
            $form->text('area_level1_id', '1级地区编码');
            $form->text('area_level2_id', '2级地区编码');
            $form->text('area_level3_id', '3级地区编码');
            $form->text('area_level4_id', '4级地区编码');
//        $form->switch('area_fixed', '地区码锁定');
//        $form->switch('poweroff_send_type', '断电发送类型')->default(1);
//        $form->switch('offline_send_type', '离线发送类型')->default(2);
//        $form->number('offline_send_warninger_id', '离线发送报警通道');
//        $form->number('sub_count', '');
//        $form->number('category_count', 'Category count');
//        $form->number('category_count_has_cooler', 'Category count has cooler');


        })->tab('CDC相关',function ($form){
            //        $form->switch('cdc_settingedit_menu', 'Cdc settingedit menu')->default(1);
//        $form->switch('auto_block_menu', 'Auto block menu');
            $form->switch('cdc_admin', '疾控管理账户');
            $form->text('nipis_code', '疾控NIPIS编码');
            $form->number('cdc_level', '疾控级别cdc_level');
            $form->number('cdc_map_level', '疾控地图级别cdc_map_level')->default(1);
            $form->text('bigkey', '第一版大屏密钥');
            $form->text('big_action', '第一版大屏首页')->default('index');
//        $form->text('domain', '独立域名（废弃）');
            $form->number('sort', '排序');
//        $form->text('server_alert', '服务弹窗并弹窗');
//        $form->switch('server_out_date_open', '到期提醒');
//        $form->date('server_out_date', '到期时间')->default(date('Y-m-d'));
//        $form->date('sensor_out_date', '探头校准到期时间')->default(date('Y-m-d'));
//        $form->number('status_collectors_num', 'Status collectors num');
//        $form->number('status_temp_high', 'Status temp high');
//        $form->number('status_temp_low', 'Status temp low');
//        $form->number('status_offline', 'Status offline');
            $form->text('index_title', '首页饼状图标题');
//        $form->textarea('services_note', 'Services note');
            $form->switch('closed', '关闭服务并弹窗');
            $form->text('closed_warning', '关闭服务弹窗的提示语');
            $form->switch('sign_contract', '签约客户');
//        $form->switch('show_in_list', 'Show in list')->default(1);
//        $form->text('region_name', '地区名称');
            $form->switch('svg_map', 'svg map 等级');
            $form->text('svg_map_name', 'Svg map name');
//        $form->decimal('svg_map_x', 'Svg map x');
//        $form->decimal('svg_map_y', 'Svg map y');
        })->tab('菜单开关',function ($form){
            $form->switch('common_menu', '通用菜单')->default(1);
            $form->switch('vehicle_menu', '易流车载菜单');
            $form->switch('couveuse_menu', '便携保温箱菜单');
            $form->switch('mobile_menu', '便携车载菜单');
            $form->switch('printer_menu', '打印机菜单');
            $form->switch('setting_menu', '设置菜单');
            $form->switch('super_warninger_menu', '超级报警菜单');
            $form->switch('volt_warning_menu', '电压报警菜单');
            $form->switch('weixin_sendlog_menu', '微信版发送记录')->default(1);
            $form->switch('video_menu', '视频菜单');
            $form->switch('cdc_category_menu', 'CDC分类菜单');
            $form->switch('cdc_cooleredit_menu', 'CDC编辑冰箱信息权限');
            $form->switch('cdc_warningeredit_menu', 'CDC编辑报警通道权限');
            $form->switch('not_care_offline_menu', '离线巡检');
            $form->switch('not_show_offline_menu', '不显示离线');
            $form->switch('login_sms_check_menu', '开启短信验证才能登录');
            $form->switch('addon_menu', '第一版应用中心');
        })->tab('其他',function ($form){
            $form->text('custome_code', '客户码（前期记录中继器设置）');

        });



        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }

    public function checkRemindLogin(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());
        if ($value == 1) {
            $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->doesntHave('remindRules');
        } else {
            $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->whereHas('remindRules');
        }
        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->address('地址');

        $grid->disableCreateButton();

        $grid->disableActions();
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $rules = RemindLoginRule::where('status', 1)->select(['category', DB::raw('count(1) as cnt')])->groupBy('category')->get();
                foreach ($rules as $rule) {
                    if($value==1)
                    {
                        $batch->add('添加 【' . $rule->category . '】 的 ' . $rule->cnt . '条规则', new UpdateField(route('ccrp.companies.create_remind_login'), 'category', $rule->category));
                    }else{
                        $batch->add('删除 【' . $rule->category . '】 的 ' . $rule->cnt . '条规则', new UpdateField(route('ccrp.companies.remove_remind_login'), 'category', $rule->category));
                    }
                }
            });
        });

        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 的微信登录提醒：' . ($value == 1 ? "未开启" : "开启") . '提醒的')
            ->description('不含暂停的单位')
            ->body($grid);
    }
    public function checkWarningerBodyLimit(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());
        $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->where('warninger_body_limit','!=',$value);

        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->warninger_body_limit('一级报警联系人人数限制');

        $grid->disableCreateButton();

        $grid->disableActions();
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $batch->add(' 一级报警联系人人数限制 设置为：' . ($value) . '人', new UpdateField(route('ccrp.companies.update_field'), 'warninger_body_limit', $value));
            });
        });
        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 一级报警联系人人数限制不是：' . ($value) . '人的')
            ->description('不含暂停的单位')
            ->body($grid);

    }
    public function checkCompanyUseSettings(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());
        $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->whereDoesntHave('useSettings', function (Builder $query) use ($value) {
            $query->where('company_use_settings.value', $value);
        });

        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->setting('设置')->display(function ($value) use ($setting){
            $use_setting = $this->getUseSettings($setting->id);
            return $use_setting?$use_setting->value:'无';
        });

        $grid->disableCreateButton();

        $grid->disableActions();
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value,$setting) {
            $tools->batch(function ($batch) use ($value,$setting ) {
                $batch->disableDelete();
                $batch->add($setting->name . '：' . ($value) . '', new UpdateField(route('ccrp.company.set_company_use_settings'), $setting->id, $value));
            });
        });
        $grid->exporter($this->excel());
        return $content
            ->header($company->title .' '. $setting->name.' 不是：' . ($value) . '的')
            ->description('不含暂停的单位')
            ->body($grid);

    }

    public function checkEquipmentChangeNeedVerify(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());
        $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->whereDoesntHave('useSettings', function (Builder $query)  use ($value,$setting) {
            $query->where('company_use_settings.value', $value)->where('company_use_settings.setting_id',$setting->id);
        });
        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->address('地址');

        $grid->disableCreateButton();

        $grid->disableActions();

        $grid->tools(function ($tools) use ($value,$setting) {
            $tools->batch(function ($batch) use ($value,$setting ) {
                $batch->disableDelete();
                $batch->add($setting->name . '：' . ($value == 1 ? "需要" : "不需要")  . '审核' . '', new UpdateField(route('ccrp.company.set_company_use_settings'), $setting->id, $value));
            });
        });
        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 冷链变更单：' . ($value == 1 ? "不需要" : "需要") . ' 审核')
            ->description('不含暂停的单位')
            ->body($grid);
    }
    public function checkForbiddenWeixin(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());

        $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->whereDoesntHave('useSettings', function (Builder $query) use ($value,$setting) {
            $query->where('company_use_settings.value', $value)->where('company_use_settings.setting_id',$setting->id);
        });
        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->address('地址');

        $grid->disableCreateButton();

        $grid->disableActions();

        $grid->tools(function ($tools) use ($value,$setting) {
            $tools->batch(function ($batch) use ($value,$setting ) {
                $batch->disableDelete();
                $batch->add($setting->name . '：' . ($value == 1 ? "禁用" : "解除禁用")  . '微信端功能' . '', new UpdateField(route('ccrp.company.set_company_use_settings'), $setting->id, $value));
            });
        });
        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 冷链变更单：' . ($value == 1 ? "不禁用" : "禁用") . ' 微信端功能的单位')
            ->description('不含暂停的单位')
            ->body($grid);
    }
    public function checkCoolerHasVaccineTagsManage(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());
        $grid->model()->whereIn('id', $company->ids())->where('cdc_admin', 0)->where('status', Company::状态正常)->whereDoesntHave('useSettings', function (Builder $query) use ($value,$setting) {
            $query->where('company_use_settings.value', $value)->where('company_use_settings.setting_id',$setting->id);
        });
        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->address('地址');

        $grid->disableCreateButton();

        $grid->disableActions();

        $grid->tools(function ($tools) use ($value,$setting) {
            $tools->batch(function ($batch) use ($value,$setting ) {
                $batch->disableDelete();
                $batch->add($setting->name . '：' . ($value == 1 ? "开启" : "关闭")  . '' . '', new UpdateField(route('ccrp.company.set_company_use_settings'), $setting->id, $value));
            });
        });
        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 设备存放疫苗标签功能（货位）：' . ($value == 1 ? "关闭的" : "开启的") . ' ')
            ->description('不含暂停的单位')
            ->body($grid);
    }

    protected function setCompanyUseSettings(Request $request)
    {
        $setting_id = $request->get('field');
        $value = $request->get('value');
        foreach ($request->get('ids') as $company_id) {
            $where['company_id'] = $company_id;
            $where['setting_id'] = $setting_id;
            $set['created_at'] = Carbon::now()->toTimeString();
            $setting = CompanyUseSetting::firstOrNew($where);
            $setting->value = $value;
            $setting->save();
        }
    }

    protected function createRemindLogin(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $rules = RemindLoginRule::where($field, $value)->get();
        foreach (Company::find($request->get('ids')) as $item) {
            foreach ($rules as $rule) {
                $remind = new CompanyHasRemindLogin();
                $remind->rule_id = $rule->id;
                $remind->company_id = $item->id;
                $remind->save();
            }
        }
    }
    protected function removeRemindLogin(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $rules = RemindLoginRule::where($field, $value)->get();
        foreach (Company::find($request->get('ids')) as $item) {
            foreach ($rules as $rule) {
                $remind = CompanyHasRemindLogin::where('rule_id',$rule->id)->where('company_id',$item->id)->first();
               if($remind)
               {
                   $remind->delete();
               }
            }
        }
    }

    protected function updateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        foreach (Company::find($request->get('ids')) as $item) {
            $item->{$field} = $value;
            $item->save();
        }
    }

    public function createCdcAdminByArea(Area $area)
    {
        $parent_area = $area->parent;
        $parent_count = Company::where('region_code',$parent_area->id)->where('cdc_admin',1)->count();
        if($parent_count !=1 )
        {
            $content = new Content();
            $form = new Form(new Company);
            $form->setAction('?');
            $form->select('pid', '账户状态')->options(Company::where('region_code',$parent_area->id)->pluck('title','id'));
            return $content
                ->header('编辑单位')
                ->description('description')
                ->body($form);
        }else{
            $parent = Company::where('region_code',$parent_area->id)->first();
            $rs = $parent->addSubCdcCompany($area);
            return $rs;
        }
    }

    public function tag(Request $request)
    {
        foreach (Company::find($request->get('ids')) as $company) {
            $company->tags()->syncWithoutDetaching($request->get('tags'));
        }
    }

    public function tools($id, Content $content)
    {
        $show = new Show($company = Company::findOrFail($id));

        $show->id('Id');
        $show->title('单位名称');
        $show->coolers('所有冰箱冷库')->as(function ($item){
            return '<a href="'.'/admin/ccrp/coolers?company_id='.$this->id.'">所有冰箱冷库</a>';
        })->link();
        $show->cooler_month('人工签名')->as(function ($item){
            return '<a href="'.'/admin/ccrp/reports/stat_monthly?&company_id='.$this->id.'&year='.date('Y').'&month='.date('m').'&day='.date('d').'&sign_time_a='.strtoupper(date('a')).'">查看今日人工签名</a>';
        })->link();

        $show->contacts('报警联系人')->as(function ($item){
            return '<a href="'.'/admin/ccrp/contacts?company_id='.$this->id.'">单位报警联系人</a>';
        })->link();


        $show->contacts('小程序绑定人员')->as(function ($item){
            return '<a href="'.'/admin/ucenter/users?4eccd5d84db1ef8febc1131cc3aa2270%5B%5D=1&ed4e350a61224310f6888414d0f2a383='.$this->id.'">小程序绑定人员</a>';
        })->link();

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
                $tools->disableEdit();
            });

        return $content
            ->header('工具')
            ->description($company->title)
            ->body($show);
    }

}
