<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sys\Setting;
use App\Models\Ccrp\WarningSetting;
use App\Http\Controllers\Controller;
use function App\Utils\format_time;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;
use Illuminate\Http\Request;

class WarningSettingsController extends Controller
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
        $grid = new Grid(new WarningSetting);

        $grid->id('Id');
        $grid->collector_id('Collector id');
        $grid->warning_strategy_id('Warning strategy id');
        $grid->temp_warning('Temp warning');
        $grid->humi_warning('Humi warning');
        $grid->volt_warning('Volt warning');
        $grid->temp_high('Temp high');
        $grid->temp_low('Temp low');
        $grid->humi_high('Humi high');
        $grid->humi_low('Humi low');
        $grid->volt_high('Volt high');
        $grid->volt_low('Volt low');
        $grid->temp_warning_last('Temp warning last');
        $grid->temp_warning2_last('Temp warning2 last');
        $grid->temp_warning3_last('Temp warning3 last');
        $grid->humi_warning_last('Humi warning last');
        $grid->humi_warning2_last('Humi warning2 last');
        $grid->humi_warning3_last('Humi warning3 last');
        $grid->volt_warning_last('Volt warning last');
        $grid->set_time('Set time');
        $grid->set_uid('Set uid');
        $grid->warninger_id('Warninger id');
        $grid->warninger2_id('Warninger2 id');
        $grid->warninger3_id('Warninger3 id');
        $grid->category_id('Category id');
        $grid->company_id('Company id');
        $grid->status('Status');
        $grid->note('Note');

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
        $show = new Show(WarningSetting::findOrFail($id));

        $show->id('Id');
        $show->collector_id('Collector id');
        $show->warning_strategy_id('Warning strategy id');
        $show->temp_warning('Temp warning');
        $show->humi_warning('Humi warning');
        $show->volt_warning('Volt warning');
        $show->temp_high('Temp high');
        $show->temp_low('Temp low');
        $show->humi_high('Humi high');
        $show->humi_low('Humi low');
        $show->volt_high('Volt high');
        $show->volt_low('Volt low');
        $show->temp_warning_last('Temp warning last');
        $show->temp_warning2_last('Temp warning2 last');
        $show->temp_warning3_last('Temp warning3 last');
        $show->humi_warning_last('Humi warning last');
        $show->humi_warning2_last('Humi warning2 last');
        $show->humi_warning3_last('Humi warning3 last');
        $show->volt_warning_last('Volt warning last');
        $show->set_time('Set time');
        $show->set_uid('Set uid');
        $show->warninger_id('Warninger id');
        $show->warninger2_id('Warninger2 id');
        $show->warninger3_id('Warninger3 id');
        $show->category_id('Category id');
        $show->company_id('Company id');
        $show->status('Status');
        $show->note('Note');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WarningSetting);

        $form->number('collector_id', 'Collector id');
        $form->number('warning_strategy_id', 'Warning strategy id');
        $form->switch('temp_warning', 'Temp warning')->default(1);
        $form->switch('humi_warning', 'Humi warning');
        $form->switch('volt_warning', 'Volt warning');
        $form->decimal('temp_high', 'Temp high')->default(99.00);
        $form->decimal('temp_low', 'Temp low')->default(-99.00);
        $form->decimal('humi_high', 'Humi high')->default(99.99);
        $form->decimal('humi_low', 'Humi low')->default(0.00);
        $form->decimal('volt_high', 'Volt high')->default(8.80);
        $form->decimal('volt_low', 'Volt low')->default(2.60);
        $form->number('temp_warning_last', 'Temp warning last')->default(30);
        $form->switch('temp_warning2_last', 'Temp warning2 last')->default(60);
        $form->switch('temp_warning3_last', 'Temp warning3 last')->default(90);
        $form->number('humi_warning_last', 'Humi warning last')->default(30);
        $form->number('humi_warning2_last', 'Humi warning2 last');
        $form->number('humi_warning3_last', 'Humi warning3 last');
        $form->number('volt_warning_last', 'Volt warning last')->default(30);
        $form->number('set_time', 'Set time');
        $form->number('set_uid', 'Set uid');
        $form->number('warninger_id', 'Warninger id');
        $form->number('warninger2_id', 'Warninger2 id');
        $form->number('warninger3_id', 'Warninger3 id');
        $form->number('category_id', 'Category id');
        $form->number('company_id', 'Company id');
        $form->switch('status', 'Status')->default(1);
        $form->text('note', 'Note');

        return $form;
    }

    protected function checkTemp(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new WarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where('temp_high', '<>', $value)
            ->WhereHas('Collector', function ($query) use ($value) {
                $query->where('status', '1')->where('temp_type', 2);
            })
            ->orderBy('company_id', 'asc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->collector()->collector_name('探头名称');
        $grid->temp_warning('温度报警')->using(WarningSetting::TEMP_WARNING);
        $grid->temp_high('温度上限')->display(function ($value) {
            return '<span class="label label-danger">' . $value . '</value>';
        });
        $grid->temp_low('温度下限');
        $grid->temp_warning_last('一级延迟');
        $grid->temp_warning2_last('二级延迟');
        $grid->temp_warning3_last('三级延迟');
        $grid->set_time('设置时间')->display(function ($value) {
            return date('Y-m-d H:i', $value);
        });
        $grid->warninger_id('Warninger id');
        $grid->status('状态')->using(WarningSetting::STATUS);
        $grid->note('Note');

        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();

        });
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $batch->add('探头预警温度上限设置为' . $value . '°C', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_high', $value));
            });
        });
//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . '  冷冻探头的预警温度上限不是' . $value . '°C')
            ->description('')
            ->body($grid);
    }
    protected function checkTempCoolRange(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $values = explode(',',$value);
        $grid = new Grid(new WarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where(function($query) use ($values){

                $query->where('temp_high', '<>', $values[1])
                     ->orWhere('temp_low', '<>', $values[0]);
            })
            ->WhereHas('Collector', function ($query) use ($value) {
                $query->where('status', '1')->where('temp_type', 1);
            })
            ->orderBy('company_id', 'asc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->collector()->collector_name('探头名称');
        $grid->temp_warning('温度报警')->using(WarningSetting::TEMP_WARNING);
        $grid->temp_high('温度上限')->display(function ($value) {
            return '<span class="label label-danger">' . $value . '</value>';
        });
        $grid->temp_low('温度下限')->display(function ($value) {
            return '<span class="label label-danger">' . $value . '</value>';
        });
        $grid->temp_warning_last('一级延迟');
        $grid->temp_warning2_last('二级延迟');
        $grid->temp_warning3_last('三级延迟');
        $grid->set_time('设置时间')->display(function ($value) {
            return date('Y-m-d H:i', $value);
        });
        $grid->warninger_id('Warninger id');
        $grid->status('状态')->using(WarningSetting::STATUS);
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
                $batch->add('探头预警温度上限设置为' . $values[1] . '°C', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_high', $values[1]));
                $batch->add('探头预警温度下限设置为' . $values[0] . '°C', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_low', $values[0]));
            });
        });
//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . '  冷藏探头的预警温度上限不是' . $value . '°C')
            ->description('')
            ->body($grid);
    }

    protected function checkTempColdRange(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $values = explode(',',$value);
        $grid = new Grid(new WarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where(function($query) use ($values){

                $query->where('temp_high', '<>', $values[1])
                     ->orWhere('temp_low', '<>', $values[0]);
            })
            ->WhereHas('Collector', function ($query) use ($value) {
                $query->where('status', '1')->where('temp_type', 2);
            })
            ->orderBy('company_id', 'asc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->collector()->collector_name('探头名称');
        $grid->temp_warning('温度报警')->using(WarningSetting::TEMP_WARNING);
        $grid->temp_low('温度下限')->display(function ($value) {
            return '<span class="label label-danger">' . $value . '</value>';
        });
        $grid->temp_high('温度上限')->display(function ($value) {
            return '<span class="label label-danger">' . $value . '</value>';
        });
        $grid->collector()->temp('最新温度');
        $grid->temp_warning_last('一级延迟');
        $grid->temp_warning2_last('二级延迟');
        $grid->temp_warning3_last('三级延迟');
        $grid->set_time('设置时间')->display(function ($value) {
            return date('Y-m-d H:i', $value);
        });
        $grid->warninger_id('Warninger id');
        $grid->status('状态')->using(WarningSetting::STATUS);
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
                $batch->add('探头预警温度上限设置为' . $values[1] . '°C', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_high', $values[1]));
                $batch->add('探头预警温度下限设置为' . $values[0] . '°C', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_low', $values[0]));
            });
        });
//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . '  冷冻探头的预警温度上下限不是' . $value . '°C')
            ->description('')
            ->body($grid);
    }

    protected function checkTempWarningLast(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value_str = $diy->value ?? $setting->value;
        $values = explode(',', $value_str);
        $grid = new Grid(new WarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where(function ($query) use ($values) {
                $query->where('temp_warning_last', '<>', $values[0])
                    ->orWhere('temp_warning2_last', '<>', $values[1])
                    ->orWhere('temp_warning3_last', '<>', $values[2]);
            })
            ->WhereHas('Collector', function ($query) {
                $query->where('status', '1');
            })
            ->orderBy('company_id', 'asc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->collector()->collector_name('探头名称');
        $grid->temp_warning('温度报警')->using(WarningSetting::TEMP_WARNING);

        $grid->temp_warning_last('一级延迟')->display(function ($value) use ($values) {
            if ($value != $values[0]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->temp_warning2_last('二级延迟')->display(function ($value) use ($values) {
            if ($value != $values[1]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->temp_warning3_last('三级延迟')->display(function ($value) use ($values) {
            if ($value != $values[2]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->set_time('设置时间')->display(function ($value) {
            return date('Y-m-d H:i', $value);
        });
        $grid->warninger_id('Warninger id');
        $grid->status('状态')->using(WarningSetting::STATUS);
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

                $batch->add('设置一级报警延迟为：' . $values[0] . '分钟', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_warning_last', $values[0] ));
                $batch->add('设置二级报警延迟为：' . $values[1]  . '分钟', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_warning2_last', $values[1]));
                $batch->add('设置三级报警延迟为：' . $values[2] . '分钟', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_warning3_last', $values[2]));
            });
        });
//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . '  探头报警延迟不是：' . $value_str . '分钟的')
            ->description('')
            ->body($grid);
    }
    protected function check_has_certification(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value_str = $diy->value ?? $setting->value;
        $values = explode(',', $value_str);
        $grid = new Grid(new WarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where(function ($query) use ($values) {
                $query->where('temp_warning_last', '<>', $values[0])
                    ->orWhere('temp_warning2_last', '<>', $values[1])
                    ->orWhere('temp_warning3_last', '<>', $values[2]);
            })
            ->WhereHas('Collector', function ($query) {
                $query->where('status', '1')->where('temp_type', 2);
            })
            ->orderBy('company_id', 'asc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->collector()->collector_name('探头名称');
        $grid->temp_warning('温度报警')->using(WarningSetting::TEMP_WARNING);

        $grid->temp_warning_last('一级延迟')->display(function ($value) use ($values) {
            if ($value != $values[0]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->temp_warning2_last('二级延迟')->display(function ($value) use ($values) {
            if ($value != $values[1]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->temp_warning3_last('三级延迟')->display(function ($value) use ($values) {
            if ($value != $values[2]) {
                return '<span class="label label-danger">' . $value . '</value>';
            } else {
                return $value;
            }
        });
        $grid->set_time('设置时间')->display(function ($value) {
            return date('Y-m-d H:i', $value);
        });
        $grid->warninger_id('Warninger id');
        $grid->status('状态')->using(WarningSetting::STATUS);
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

            });
        });
//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . '  探头报警延迟不是：' . $value_str . '分钟的')
            ->description('')
            ->body($grid);
    }

    protected function updateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        foreach (WarningSetting::find($request->get('ids')) as $item) {
            $item->{$field} = $value;
            $item->save();
        }
    }

    protected function checkTempWarning(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new WarningSetting());
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where('status', WarningSetting::预警开启);
        if($value==0)
        {
            $grid->model()->where('temp_warning',1);
        }else{
            $grid->model()->where('temp_warning',0);
        };
        $grid->model()->orderBy('company_id', 'asc');

        $grid->id('Id');
        $grid->collector()->collector_id('Collector id');
        $grid->company()->title('单位名称');
        $grid->collector()->cooler_name('冰箱名称');
        $grid->collector()->collector_name('探头名称');
        $grid->collector()->supplier_collector_id('探头编号');
        $grid->warninger_id('报警通道');
        $grid->status('报警设置状态')->display(function($value){return $value?'正常':'关闭';});
        $grid->temp_warning('温度报警')->display(function($value){return $value?'开启':'关闭';});
        $grid->collector()->temp('温度');
        $grid->collector()->humi('湿度');
        $grid->collector()->rssi('信号');
        $grid->collector()->refresh_time('更新时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->collector()->sinstall_time('安装时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->collector()->status('探头状态')->using(Collector::STATUSES);
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

        });


        $grid->tools(function ($tools) use ($value,$setting) {
            $tools->batch(function ($batch) use ($value,$setting ) {
                $batch->disableDelete();
                $batch->add($setting->name . '：' . ($value==1?"开启":"关闭") . '', new UpdateField(route('ccrp.warning_settings.update_field'), 'temp_warning', $value));
            });
        });

        return $content
            ->header($company->title . ' 的所有探头（'.($value==1?"未":"").'开启超温预警的）')
            ->description('不包含报废的')
            ->body($grid);
    }
}
