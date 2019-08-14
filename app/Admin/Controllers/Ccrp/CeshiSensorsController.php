<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\CeshiSensor;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class CeshiSensorsController extends Controller
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
            ->header('探头测试记录')
            ->description('ceshi.lengwang.net')
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
        $grid = new Grid(new CeshiSensor);

        $grid->model()->orderBy('id','desc');
        $grid->id('Id');
        $grid->sensor_id('探头序号');
        $grid->sensor_sn('探头传输sn');
        $grid->temp('温度');
        $grid->humi('湿度');
        $grid->volt('电压');
        $grid->rssi('信号');
        $grid->sender_volt('主机电压');
        $grid->sender_id('主机序号');
        $grid->sender_sn('主机传输sn');
        $grid->sensor_collect_time('探头采集时间')->display(function ($value){return Carbon::createFromTimestamp($value)->toDateTimeString();});
        $grid->sensor_trans_time('探头传输时间')->display(function ($value){return Carbon::createFromTimestamp($value)->toDateTimeString();});
        $grid->sender_trans_time('主机发送时间')->display(function ($value){return Carbon::createFromTimestamp($value)->toDateTimeString();});
        $grid->system_time('系统时间')->display(function ($value){return Carbon::createFromTimestamp($value)->toDateTimeString();});
        $grid->ceshi_time('测试时间')->display(function ($value){return Carbon::createFromTimestamp($value)->toDateTimeString();});
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('sensor_id', '探头序号');
            $filter->equal('sender_id', '主机序号');

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
        $show = new Show(CeshiSensor::findOrFail($id));

        $show->id('Id');
        $show->sensor_id('Sensor id');
        $show->sensor_sn('Sensor sn');
        $show->temp('Temp');
        $show->humi('Humi');
        $show->volt('Volt');
        $show->rssi('Rssi');
        $show->sender_volt('Sender volt');
        $show->sender_id('Sender id');
        $show->sender_sn('Sender sn');
        $show->sensor_collect_time('Sensor collect time');
        $show->sensor_trans_time('Sensor trans time');
        $show->sender_trans_time('Sender trans time');
        $show->system_time('System time');
        $show->isadd('Isadd');
        $show->ceshi_time('Ceshi time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CeshiSensor);

        $form->text('sensor_id', 'Sensor id');
        $form->text('sensor_sn', 'Sensor sn');
        $form->text('temp', 'Temp');
        $form->text('humi', 'Humi');
        $form->text('volt', 'Volt');
        $form->text('rssi', 'Rssi');
        $form->text('sender_volt', 'Sender volt');
        $form->text('sender_id', 'Sender id');
        $form->number('sender_sn', 'Sender sn');
        $form->number('sensor_collect_time', 'Sensor collect time');
        $form->number('sensor_trans_time', 'Sensor trans time');
        $form->number('sender_trans_time', 'Sender trans time');
        $form->number('system_time', 'System time');
        $form->number('isadd', 'Isadd');
        $form->number('ceshi_time', 'Ceshi time');

        return $form;
    }
}
