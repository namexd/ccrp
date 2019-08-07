<?php

namespace App\Admin\Controllers\Reports;

use App\Models\Ccrp\Reports\TaskRemindLoginTask;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TaskRemindLoginTasksController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Ccrp\Reports\TaskRemindLoginTask';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TaskRemindLoginTask);

        $grid->model()->orderBy('id','desc');
        $grid->column('id', __('Id'));
        $grid->column('company.title', __('Company id'));
        $grid->column('rule.category', __('Rule id'));
        $grid->column('remind_time', __('Remind time'));
        $grid->column('remind_date', __('Remind date'));
        $grid->column('wxcode', __('Wxcode'));
        $grid->column('title', __('Title'));
        $grid->column('content', __('Content'));
        $grid->column('create_time', __('Create time'))->display(function($value){ return date('Y-m-d H:i',$value);});
        $grid->column('send_time', __('Send time'))->display(function($value){ return date('Y-m-d H:i',$value);});
        $grid->column('send_status', __('Send status'));
        $grid->filter(function ($filter) {
//            $filter->disableIdFilter();
            $filter->equal('company_id', '单位id');
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
        $show = new Show(TaskRemindLoginTask::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('company_id', __('Company id'));
        $show->field('rule_id', __('Rule id'));
        $show->field('remind_time', __('Remind time'));
        $show->field('remind_date', __('Remind date'));
        $show->field('wxcode', __('Wxcode'));
        $show->field('title', __('Title'));
        $show->field('content', __('Content'));
        $show->field('create_time', __('Create time'));
        $show->field('send_time', __('Send time'));
        $show->field('send_status', __('Send status'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TaskRemindLoginTask);

        $form->number('company_id', __('Company id'));
        $form->number('rule_id', __('Rule id'));
        $form->time('remind_time', __('Remind time'))->default(date('H:i:s'));
        $form->date('remind_date', __('Remind date'))->default(date('Y-m-d'));
        $form->text('wxcode', __('Wxcode'));
        $form->text('title', __('Title'));
        $form->text('content', __('Content'));
        $form->number('create_time', __('Create time'));
        $form->number('send_time', __('Send time'));
        $form->switch('send_status', __('Send status'));

        return $form;
    }
}
