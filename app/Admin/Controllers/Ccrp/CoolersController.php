<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\CompanyUseSetting;
use App\Models\Ccrp\Cooler;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\Sys\Setting;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CoolersController extends Controller
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
            ->header('冷链装备')
            ->description('冰箱冷库')
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
            ->header('Edit')
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
        $grid = new Grid(new Cooler);

        $grid->cooler_id('Id');
        $grid->company_id('单位id');
        $grid->company()->title('单位名称');
        $grid->cooler_sn('设备编码');
        $grid->nipis_code('NIPIS编码');
        $grid->cooler_name('设备名称');
        $grid->cooler_type('设备类型')->using(Cooler::COOLER_TYPE);
        $grid->cooler_brand('品牌');
        $grid->cooler_model('型号');
        $grid->is_medical('是否医用')->using(Cooler::IS_MEDICAL);
        $grid->status('状态')->using(Cooler::$status);

        $grid->filter(function ($filter){
            /* @var $filter Grid\Filter */
            $filter->disableIdFilter();
            $filter->equal('company_id','单位id');
            $filter->equal('status','状态')->radio(Cooler::$status);
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->append('<br><a target="_blank" href="' . route('ccrp.login', $actions->row->company_id) . '"> <i class="fa fa-laptop"></i></a>');
            $actions->append('<br><a target="_blank" href="' . route('cooler_details.index') . '?cooler_id='.$actions->row->cooler_id.'"> <i class="fa fa-book"></i></a>');
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
        $show = new Show(Cooler::findOrFail($id));

        $show->cooler_id('Cooler id');
        $show->cooler_category_id2('Cooler category id2');
        $show->cooler_sn('Cooler sn');
        $show->nipis_code('NIPIS code');
        $show->cdc_code('CDC code');
        $show->cooler_name('Cooler name');
        $show->cooler_type('Cooler type');
        $show->cooler_img('Cooler img');
        $show->cooler_brand('Cooler brand');
        $show->cooler_size('Cooler size');
        $show->cooler_size2('Cooler size2');
        $show->cooler_model('Cooler model');
        $show->is_medical('Is medical');
        $show->door_type('Door type');
        $show->cooler_starttime('Cooler starttime');
        $show->cooler_fillingtime('Cooler fillingtime');
        $show->category_id('Category id');
        $show->company_id('Company id');
        $show->temp_warning('Temp warning');
        $show->humi_warning('Humi warning');
        $show->volt_warning('Volt warning');
        $show->update_time('Update time');
        $show->install_time('Install time');
        $show->install_uid('Install uid');
        $show->uninstall_time('Uninstall time');
        $show->warning_times('Warning times');
        $show->collector_num('Collector num');
        $show->temp_collector_num('Temp collector num');
        $show->humi_collector_num('Humi collector num');
        $show->come_from('Come from');
        $show->status('Status');
        $show->sort('Sort');
        $show->cooler_monitortime('Cooler monitortime');
        $show->xunjian_time('Xunjian time');
        $show->cooler_name_bk('Cooler name bk');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Cooler);

//        $form->number('cooler_category_id2', 'Cooler category id2');
        $form->text('cooler_sn', '冰箱SN');
        $form->text('nipis_code', '疾控编码');
        $form->text('cooler_name', '冰箱名称');
        $form->select('cooler_type', '冰箱类型')->options(Cooler::COOLER_TYPE);
//        $form->text('cooler_img', 'Cooler img');
        $form->text('cooler_brand', '冰箱品牌');
        $form->text('cooler_model', '型号');
        $form->radio('is_medical', '')->options(Cooler::IS_MEDICAL);
        $form->text('cooler_size', '冷藏容积');
        $form->text('cooler_size2', '冷冻容积');
        $form->text('come_from', '来源');
        return $form;
    }

    public function offlineCheck(Setting $setting, Company $company, Content $content)
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
                $batch->add($setting->name . '：' . ($value == 1 ? "需要" : "不需要")  . '开启' . '', new UpdateField(route('ccrp.cooler.set_offline_check'), $setting->id, $value));
            });
        });
        return $content
            ->header($company->title . ' 冰箱整体离线巡检  ：' . ($value == 1 ? "不需要" : "需要") . ' 开启')
            ->description('不含暂停的单位')
            ->body($grid);
    }


    protected function setOfflineCheck(Request $request)
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
    public function roomSign(Setting $setting, Company $company, Content $content)
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
                $batch->add($setting->name . '：' . ($value == 1 ? "需要" : "不需要")  . '开启' . '', new UpdateField(route('ccrp.cooler.set_room_sign'), $setting->id, $value));
            });
        });
        return $content
            ->header($company->title . ' 开启室温人工签名  ：' . ($value == 1 ? "不需要" : "需要") . ' 开启')
            ->description('不含暂停的单位')
            ->body($grid);
    }


    protected function setRoomSign(Request $request)
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
}
