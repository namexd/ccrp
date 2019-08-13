<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\SenderWarningSetting;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\Sys\Setting;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class SenderWarningSettingsController extends Controller
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
            ->header('主机报警设置')
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
        $grid = new Grid(new SenderWarningSetting);

        $grid->id('Id');
        $grid->sender_id('Sender id');
        $grid->power_warning('Power warning');
        $grid->power_warning_last('Power warning last');
        $grid->power_warning2_last('Power warning2 last');
        $grid->power_warning3_last('Power warning3 last');
        $grid->set_time('Set time');
        $grid->set_uid('Set uid');
        $grid->warninger_id('Warninger id');
        $grid->warninger2_id('Warninger2 id');
        $grid->warninger3_id('Warninger3 id');
        $grid->category_id('Category id');
        $grid->company_id('Company id');
        $grid->status('Status');

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
        $show = new Show(SenderWarningSetting::findOrFail($id));

        $show->id('Id');
        $show->sender_id('Sender id');
        $show->power_warning('Power warning');
        $show->power_warning_last('Power warning last');
        $show->power_warning2_last('Power warning2 last');
        $show->power_warning3_last('Power warning3 last');
        $show->set_time('Set time');
        $show->set_uid('Set uid');
        $show->warninger_id('Warninger id');
        $show->warninger2_id('Warninger2 id');
        $show->warninger3_id('Warninger3 id');
        $show->category_id('Category id');
        $show->company_id('Company id');
        $show->status('Status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SenderWarningSetting);

        $form->number('sender_id', 'Sender id');
        $form->switch('power_warning', 'Power warning')->default(1);
        $form->number('power_warning_last', 'Power warning last');
        $form->switch('power_warning2_last', 'Power warning2 last')->default(30);
        $form->switch('power_warning3_last', 'Power warning3 last')->default(60);
        $form->number('set_time', 'Set time');
        $form->number('set_uid', 'Set uid');
        $form->number('warninger_id', 'Warninger id');
        $form->number('warninger2_id', 'Warninger2 id');
        $form->number('warninger3_id', 'Warninger3 id');
        $form->number('category_id', 'Category id');
        $form->number('company_id', 'Company id');
        $form->switch('status', 'Status')->default(1);

        return $form;
    }

    protected function checkWarningLast(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value_str = $diy->value ?? $setting->value;
        $values = explode(',', $value_str);
        $grid = new Grid(new SenderWarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where(function ($query) use ($values) {
                $query->where('power_warning_last', '<>', $values[0])
                    ->orWhere('power_warning2_last', '<>', $values[1])
                    ->orWhere('power_warning3_last', '<>', $values[2]);
            })
            ->WhereHas('Sender', function ($query) {
                $query->where('status', '1');
            })
            ->where('status',1)
            ->orderBy('company_id', 'asc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->sender()->note('主机名称');
        $grid->power_warning('断电报警')->using(SenderWarningSetting::POWER_WARNING);

        $grid->power_warning_last('一级延迟')->display(function ($value) use ($values) {
            if ($value != $values[0]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->power_warning2_last('二级延迟')->display(function ($value) use ($values) {
            if ($value != $values[1]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->power_warning3_last('三级延迟')->display(function ($value) use ($values) {
            if ($value != $values[2]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->set_time('设置时间')->display(function ($value) {
            return $value?date('Y-m-d H:i', $value):'';
        });
        $grid->warninger_id('Warninger id');
        $grid->status('状态')->using(SenderWarningSetting::STATUS);
        $grid->note('Note');

        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

        });
        $grid->tools(function ($tools) use ($values) {
            $tools->batch(function ($batch) use ($values) {
                $batch->disableDelete();

                $batch->add('设置一级报警延迟为：' . $values[0] . '分钟', new UpdateField(route('ccrp.sender_warning_settings.update_field'), 'power_warning_last', $values[0] ));
                $batch->add('设置二级报警延迟为：' . $values[1]  . '分钟', new UpdateField(route('ccrp.sender_warning_settings.update_field'), 'power_warning2_last', $values[1]));
                $batch->add('设置三级报警延迟为：' . $values[2] . '分钟', new UpdateField(route('ccrp.sender_warning_settings.update_field'), 'power_warning3_last', $values[2]));
            });
        });
//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . '  主机断电报警延迟不是：' . $value_str . '分钟的')
            ->description('')
            ->body($grid);
    }

    protected function updateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        foreach (SenderWarningSetting::find($request->get('ids')) as $item) {
            $item->{$field} = $value;
            $item->save();
        }
    }
}
