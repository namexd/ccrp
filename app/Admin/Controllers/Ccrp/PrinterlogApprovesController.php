<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\PrinterlogApprove;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PrinterlogApprovesController extends Controller
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
            ->header('打印记录审核')
            ->description('重庆医药股份专用表单')
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
        $grid = new Grid(new PrinterlogApprove);

        $grid->model()->orderBy('id', 'desc');
        $grid->id('Id');
        $grid->log_id('日志编号');
        $grid->log_print_time('打印时间')->display(function($value){
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->company()->title('单位名称');
        $grid->printerlog()->title('标题');
        $grid->printerlog()->subtitle('备注');
        $grid->printerlog()->print_time('打印时间')->display(function($value){
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->temp_max('最高温');
        $grid->temp_min('最低温');
        $grid->approve_result('审核结果');
        $grid->approve_name('审核人员');
        $grid->approve_time('审核时间')->display(function($value){
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->approve_note('审核备注');


        $grid->disableRowSelector();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });


        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('log_id', '日志编号');

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
        $show = new Show(PrinterlogApprove::findOrFail($id));

        $show->id('Id');
        $show->log_id('Log id');
        $show->log_print_time('Log print time');
        $show->log_company_id('Log company id');
        $show->temp_max('Temp max');
        $show->temp_min('Temp min');
        $show->approve_result('Approve result');
        $show->approve_name('Approve name');
        $show->approve_time('Approve time')->display(function (){

        });
        $show->approve_note('Approve note');

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
        $form = new Form(new PrinterlogApprove);
        $form->switch('approve_result', '审核结果（是否通过）');
        $form->text('approve_name', '审核人');
        $form->text('approve_note', '审核备注');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
