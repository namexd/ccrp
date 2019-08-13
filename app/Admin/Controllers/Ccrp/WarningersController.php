<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sys\Setting;
use App\Models\Ccrp\Warninger;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use PHPUnit\Framework\Warning;

class WarningersController extends Controller
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
            ->header('Index')
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
        $grid = new Grid(new Warninger);

        $grid->warninger_id('Warninger id');
        $grid->warninger_name('Warninger name');
        $grid->warninger_type('Warninger type');
        $grid->warninger_type_level2('Warninger type level2');
        $grid->warninger_type_level3('Warninger type level3');
        $grid->warninger_type_tjcdc('Warninger type tjcdc');
        $grid->warninger_body('Warninger body');
        $grid->warninger_body_pluswx('Warninger body pluswx');
        $grid->warninger_body_level2('Warninger body level2');
        $grid->warninger_body_level2_pluswx('Warninger body level2 pluswx');
        $grid->warninger_body_level3('Warninger body level3');
        $grid->warninger_body_level3_pluswx('Warninger body level3 pluswx');
        $grid->warninger_body_tjcdc('Warninger body tjcdc');
        $grid->using_sensor_num('Using sensor num');
        $grid->set_time('Set time');
        $grid->set_uid('Set uid');
        $grid->bind_times('Bind times');
        $grid->category_id('Category id');
        $grid->company_id('Company id');
        $grid->mix_warning('Mix warning');
        $grid->auto_change('Auto change');
        $grid->ctime('Ctime');
        $grid->utime('Utime');

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
        $show = new Show(Warninger::findOrFail($id));

        $show->warninger_id('Warninger id');
        $show->warninger_name('Warninger name');
        $show->warninger_type('Warninger type');
        $show->warninger_type_level2('Warninger type level2');
        $show->warninger_type_level3('Warninger type level3');
        $show->warninger_type_tjcdc('Warninger type tjcdc');
        $show->warninger_body('Warninger body');
        $show->warninger_body_pluswx('Warninger body pluswx');
        $show->warninger_body_level2('Warninger body level2');
        $show->warninger_body_level2_pluswx('Warninger body level2 pluswx');
        $show->warninger_body_level3('Warninger body level3');
        $show->warninger_body_level3_pluswx('Warninger body level3 pluswx');
        $show->warninger_body_tjcdc('Warninger body tjcdc');
        $show->using_sensor_num('Using sensor num');
        $show->set_time('Set time');
        $show->set_uid('Set uid');
        $show->bind_times('Bind times');
        $show->category_id('Category id');
        $show->company_id('Company id');
        $show->mix_warning('Mix warning');
        $show->auto_change('Auto change');
        $show->ctime('Ctime');
        $show->utime('Utime');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Warninger);

        $form->text('warninger_name', 'Warninger name');
        $form->switch('warninger_type', 'Warninger type')->default(1);
        $form->switch('warninger_type_level2', 'Warninger type level2')->default(-1);
        $form->switch('warninger_type_level3', 'Warninger type level3')->default(-1);
        $form->switch('warninger_type_tjcdc', 'Warninger type tjcdc')->default(-1);
        $form->text('warninger_body', 'Warninger body');
        $form->text('warninger_body_pluswx', 'Warninger body pluswx');
        $form->text('warninger_body_level2', 'Warninger body level2');
        $form->text('warninger_body_level2_pluswx', 'Warninger body level2 pluswx');
        $form->text('warninger_body_level3', 'Warninger body level3');
        $form->text('warninger_body_level3_pluswx', 'Warninger body level3 pluswx');
        $form->text('warninger_body_tjcdc', 'Warninger body tjcdc');
        $form->number('using_sensor_num', 'Using sensor num');
        $form->number('set_time', 'Set time');
        $form->number('set_uid', 'Set uid');
        $form->number('bind_times', 'Bind times');
        $form->number('category_id', 'Category id');
        $form->number('company_id', 'Company id');
        $form->switch('mix_warning', 'Mix warning');
        $form->switch('auto_change', 'Auto change');
        $form->number('ctime', 'Ctime');
        $form->number('utime', 'Utime');

        return $form;
    }

    public function checkAutoChange(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Warninger());
        $grid->model()->whereIn('company_id', $company->ids())->where('auto_change', '<>', $value);
        $grid->warninger_id('Id');
        $grid->company()->title('单位名称');
        $grid->warninger_name('预警通道名称');
        $grid->warninger_type('预警类型')->using(Warninger::WARNINGER_TYPES);
        $grid->warninger_body('一级预警');
        $grid->warninger_body_pluswx('一级报警附加发送微信');
        $grid->warninger_body_level2('二级预警');
        $grid->warninger_body_level2_pluswx('二级报警附加发送微信');
        $grid->warninger_body_level3('三级预警');
        $grid->warninger_body_level3_pluswx('三级报警附加发送微信');
        $grid->bind_times('探头绑定数量');
        $grid->auto_change('自动切换')->using(Warninger::AUTO_CHANGE);

        $grid->disableCreateButton();

        $grid->disableActions();
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $batch->add('自动切换电话报警 设置为 ' . (Warninger::AUTO_CHANGE[$value]), new UpdateField(route('ccrp.warningers.update_field'), 'auto_change', $value));

            });
        });

        return $content
            ->header($company->title . ' 的 自动切换预警短信：' . ($value == 1 ? "不自动切换" : "自动切换") . '电话预警')
            ->description('早7：00～晚19：00发短信，其他时间打电话')
            ->body($grid);

    }
    public function checkWarningerType(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Warninger());
        $grid->model()->whereIn('company_id', $company->ids())->where('warninger_type', '<>', $value);
        $grid->warninger_id('Id');
        $grid->company()->title('单位名称');
        $grid->warninger_name('预警通道名称');
        $grid->warninger_type('预警类型')->using(Warninger::WARNINGER_TYPES);

        $grid->disableCreateButton();

        $grid->disableActions();
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $batch->add('报警类型设置为 ' . (Warninger::WARNINGER_TYPES[$value]), new UpdateField(route('ccrp.warningers.update_field'), 'warninger_type', $value));

            });
        });

        return $content
            ->header($company->title . ' 的 报警方式不是：' . ($value == 1 ? "短信" : "电话") . ' 的')
            ->description('1=短信，4=电话')
            ->body($grid);

    }

    protected function updateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        foreach (Warninger::find($request->get('ids')) as $item) {
            $item->{$field} = $value;
            $item->save();
        }
    }

}
