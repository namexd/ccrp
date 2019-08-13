<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\ExcelExpoter;
use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Collector;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sys\Setting;
use function app\Utils\format_time;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class CollectorsController extends AdminController
{
    use HasResourceActions;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Collector);

//        $grid->model()->where('id', '>', 100);
        $grid->model()->orderBy('collector_id', 'desc');

        $grid->collector_id('Collector id');
        $grid->company()->title('单位名称');
        $grid->cooler_name('冰箱名称');
        $grid->collector_name('探头名称');
        $grid->supplier_collector_id('探头编号');
        $grid->supplier_product_model('型号');
        $grid->temp('温度');
        $grid->humi('湿度');
        $grid->volt('电压');
        $grid->rssi('信号');
        $grid->isbind_sender('绑定主机?');
        $grid->bind_sender_id('绑定主机编号');
        $grid->refresh_time('更新时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->install_time('安装时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->uninstall_time('报废时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->status('状态');
        $grid->product_sn('物理编号');

        $grid->warningSetting()->warninger_id('报警通道');
        $grid->disableCreateButton();
        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
//            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->where(function ($query){
                $company=Company::find($this->input);
                $company_ids=$company->ids();
                $query->whereIn('company_id',$company_ids);
            },'单位名称')->select()->ajax('/admin/api/companies');
            $filter->like('supplier_collector_id', '探头编码');
            $filter->in('status', '状态')->checkbox([
                '1' => '正常',
                '2' => '报废',
            ])->default(['1']);
            $filter->in('isbind_sender', '绑定主机')->checkbox([
                '1' => '绑定',
                '0' => '未绑定',
            ]);
            $filter->in('supplier_product_model', '型号')->checkbox(
                Collector::SUPPLIER_PRODUCT_MODEL
            );
            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'yes':
                        // custom complex query if the 'yes' option is selected
                        $query->has('warningSetting');
                        break;
                    case 'no':
                        $query->doesntHave('warningSetting');
                        break;
                }
            }, '报警通道')->radio([
                '' => '全部',
                'no' => '未设置',
                'yes' => '已设置',
            ]);

        });
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
//            $actions->disableEdit();
            $actions->append('<a target="_blank" href="' . route('ccrp.login', $actions->row->company_id) . '"><i class="fa fa-laptop"></i></a>');

        });

        $grid->fixColumns(0, -1);
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
        $show = new Show(Collector::findOrFail($id));

        $show->collector_id('Collector id');
        $show->collector_name('Collector name');
        $show->cooler_id('Cooler id');
        $show->cooler_name('Cooler name');
        $show->supplier_id('Supplier id');
        $show->supplier_product_model('Supplier product model');
        $show->supplier_collector_id('Supplier collector id');
        $show->category_id('Category id');
        $show->company_id('Company id');
        $show->temp_warning('Temp warning');
        $show->humi_warning('Humi warning');
        $show->volt_warning('Volt warning');
        $show->temp('Temp');
        $show->humi('Humi');
        $show->volt('Volt');
        $show->rssi('Rssi');
        $show->temp_fix('Temp fix');
        $show->humi_fix('Humi fix');
        $show->temp_fix_insert('Temp fix insert');
        $show->humi_fix_insert('Humi fix insert');
        $show->update_time('Update time');
        $show->install_time('Install time');
        $show->uninstall_time('Uninstall time');
        $show->refresh_time('Refresh time');
        $show->install_uid('Install uid');
        $show->warning_times('Warning times');
        $show->warning_status('Warning status');
        $show->warning_type('Warning type');
        $show->status('Status');
        $show->sort('Sort');
        $show->warning_set('Warning set');
        $show->map_time('Map time');
        $show->map_lat('Map lat');
        $show->map_lon('Map lon');
        $show->map_address('Map address');
        $show->miss_checking('Miss checking');
        $show->offline_check('Offline check');
        $show->offline_span('Offline span');
        $show->offline_span_level2('Offline span level2');
        $show->offline_span_level3('Offline span level3');
        $show->offline_span_show('Offline span show');
        $show->certificate_no('Certificate no');
        $show->certificate_date('Certificate date');
        $show->certificate_real('Certificate real');
        $show->temp_type('Temp type');
        $show->certificate_show('Certificate show');
        $show->certificate_daynum('Certificate daynum');
        $show->note('Note');
        $show->note_time('Note time');
        $show->pgsql_date('Pgsql date');
        $show->collector_time_span('Collector time span');
        $show->collector_time_span_1('Collector time span 1');
        $show->isbind_sender('Isbind sender');
        $show->bind_sender_id('Bind sender id');
        $show->product_sn('Product sn');

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
        $form = new Form(new Collector);

        $form->text('collector_name', '探头名称')->readOnly();
//        $form->number('cooler_id', 'Cooler id');
        $form->text('cooler_name', '冰箱名称')->readOnly();
//        $form->text('supplier_id', 'Supplier id');
        $form->text('supplier_product_model', '型号')->readOnly();
        $form->text('supplier_collector_id', '编号')->readOnly();
//        $form->number('category_id', 'Category id');
//        $form->number('company_id', 'Company id');
//        $form->switch('temp_warning', 'Temp warning');
//        $form->switch('humi_warning', 'Humi warning');
//        $form->switch('volt_warning', 'Volt warning');
//        $form->decimal('temp', 'Temp');
//        $form->decimal('humi', 'Humi');
//        $form->decimal('volt', 'Volt');
//        $form->decimal('rssi', 'Rssi');
//        $form->decimal('temp_fix', 'Temp fix')->default(0.00);
//        $form->number('humi_fix', 'Humi fix');
//        $form->decimal('temp_fix_insert', 'Temp fix insert');
//        $form->decimal('humi_fix_insert', 'Humi fix insert')->default(0.00);
//        $form->number('update_time', 'Update time');
//        $form->number('install_time', 'Install time');
//        $form->number('uninstall_time', 'Uninstall time');
//        $form->number('refresh_time', 'Refresh time');
//        $form->number('install_uid', 'Install uid');
//        $form->number('warning_times', 'Warning times');
//        $form->switch('warning_status', 'Warning status');
//        $form->switch('warning_type', 'Warning type');
//        $form->switch('status', 'Status')->default(1);
//        $form->number('sort', 'Sort');
//        $form->switch('warning_set', 'Warning set');
//        $form->number('map_time', 'Map time');
//        $form->text('map_lat', 'Map lat');
//        $form->text('map_lon', 'Map lon');
//        $form->text('map_address', 'Map address');
//        $form->switch('miss_checking', 'Miss checking');
//        $form->switch('offline_check', 'Offline check')->default(1);
//        $form->number('offline_span', 'Offline span')->default(240);
//        $form->number('offline_span_level2', 'Offline span level2');
//        $form->number('offline_span_level3', 'Offline span level3');
//        $form->number('offline_span_show', 'Offline span show');
//        $form->text('certificate_no', 'Certificate no');
//        $form->date('certificate_date', 'Certificate date')->default(date('Y-m-d'));
//        $form->decimal('certificate_real', 'Certificate real');
//        $form->switch('temp_type', 'Temp type');
//        $form->decimal('certificate_show', 'Certificate show');
//        $form->number('certificate_daynum', 'Certificate daynum');
//        $form->text('note', 'Note');
//        $form->number('note_time', 'Note time');
//        $form->number('pgsql_date', 'Pgsql date');
//        $form->number('collector_time_span', 'Collector time span');
//        $form->number('collector_time_span_1', 'Collector time span 1');
        $form->switch('isbind_sender', '绑定主机？');
        $form->switch('isbind_qingxi_sender', '过滤不清洗数据？');
        $form->text('bind_sender_id', '绑定主机编号')->default(' ');
//        $form->text('product_sn', 'Product sn');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }


    protected function updateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        foreach (Collector::find($request->get('ids')) as $item) {
            $item->{$field} = $value;
            $item->save();
        }
    }

    protected function checkOfflineStatus(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Collector);
        $grid->model()->whereIn('company_id', $company->ids())->where('status', Collector::状态正常)->where('offline_check', '<>', $value);
        $grid->collector_id('Collector id');
        $grid->company()->title('单位名称');
        $grid->cooler_name('冰箱名称');
        $grid->collector_name('探头名称');
        $grid->supplier_collector_id('探头编号');
        $grid->offline_check('开启离线报警')->using(Collector::OFFLINE_STATUS);
        $grid->temp('温度');
        $grid->humi('湿度');
        $grid->rssi('信号');
        $grid->refresh_time('更新时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->install_time('安装时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->status('状态')->using(Collector::STATUSES);
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

        });
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $batch->add('离线预警状态设置为 开启', new UpdateField(route('ccrp.collectors.update_field'), 'offline_check', $value));
            });
        });

        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 的所有探头（未开启离线报警）')
            ->description('不包含报废的')
            ->body($grid);
    }


    protected function checkOfflineSpan(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Collector);
        $grid->model()->whereIn('company_id', $company->ids())->where('status', Collector::状态正常)->where('offline_span', '<>', $value);
        $grid->collector_id('Collector id');
        $grid->company()->title('单位名称');
        $grid->cooler_name('冰箱名称');
        $grid->collector_name('探头名称');
        $grid->supplier_collector_id('探头编号');
        $grid->offline_check('开启离线报警')->using(Collector::OFFLINE_STATUS);
        $grid->offline_span('离线时间');
        $grid->temp('温度');
        $grid->humi('湿度');
        $grid->rssi('信号');
        $grid->refresh_time('更新时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->install_time('安装时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->status('状态')->using(Collector::STATUSES);
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append('<a target="_blank" href="' . route('ccrp.login', $actions->row->company_id) . '"><i class="fa fa-laptop"></i></a>');

        });
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value) {
            $tools->batch(function ($batch) use ($value) {
                $batch->disableDelete();
                $batch->add('离线预警时间设置为' . $value . '分钟', new UpdateField(route('ccrp.collectors.update_field'), 'offline_span', $value));
            });
        });

        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 的所有探头（离线报警时间不是' . $value . '分钟的）')
            ->description('不包含报废的')
            ->body($grid);
    }


    protected function checkWarningStatus(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Collector);
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where('status', Collector::状态正常)
//            ->doesntHave('warningSetting')
            ->whereDoesntHave('warningSetting',function ($query) use ($value){
                $query->where('temp_warning',$value)->where('status',$value);
            })
            ->orderBy('company_id', 'asc');
        $grid->collector_id('Collector id');
        $grid->company()->title('单位名称');
        $grid->cooler_name('冰箱名称');
        $grid->collector_name('探头名称');
        $grid->supplier_collector_id('探头编号');
        $grid->warningSetting()->warninger_id('报警通道');
        $grid->warningSetting()->status('报警状态');
        $grid->warningSetting()->temp_warning('温度报警');
        $grid->temp('温度');
        $grid->humi('湿度');
        $grid->rssi('信号');
        $grid->refresh_time('更新时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->install_time('安装时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->status('状态')->using(Collector::STATUSES);
        $grid->disableCreateButton();

        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

        });

        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 的所有探头（未开启超温预警）')
            ->description('不包含报废的')
            ->body($grid);
    }


    protected function checkHasCertification(Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Collector);
        $grid->model()
            ->whereIn('company_id', $company->ids())
            ->where('status', Collector::状态正常)
//            ->doesntHave('warningSetting')
            ->doesntHave('certifications')
            ->orderBy('company_id', 'asc');
        $grid->collector_id('Collector id');
        $grid->company()->title('单位名称');
        $grid->cooler_name('冰箱名称');
        $grid->collector_name('探头名称');
        $grid->supplier_collector_id('探头编号');
        $grid->refresh_time('更新时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->install_time('安装时间')->display(function ($value) {
            return format_time($value);
        });
        $grid->status('状态')->using(Collector::STATUSES);
        $grid->disableCreateButton();

        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

        });

        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 的所有探头（没有第三方校准证书的）')
            ->description('不包含报废的')
            ->body($grid);
    }


    private function excel()
    {
        $excel = new ExcelExpoter();
        $excel->setFileName('探头');
        $columns = [
            'collector_id' => '探头ID',
            'company_id' => '单位id',
            'company.title' => '单位名称',
            'cooler_name' => '冰箱冷库名称',
            'collector_name' => '探头名称',
            'supplier_collector_id' => '探头编号',
            'offline_check' => '离线预警',
            'offline_span' => '离线预警时间',
            'install_time' => '安装时间',
            'refresh_time' => '刷新时间',
        ];
        $excel->setColumn($columns);
        $excel->setColumnFormat([
            'offline_check' => ExcelExpoter::单元格格式是否,
        ]);
        $excel->setColumnTransfer([
            'install_time' => ExcelExpoter::转换格式时间戳成日期,
            'refresh_time' => ExcelExpoter::转换格式时间戳成时间,
        ]);
        return $excel;
    }
}
