<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\PhysicalConfig;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PhysicalConfigsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '冷链体检配置';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PhysicalConfig);

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('weight','权重');
        $grid->column('description', __('Description'));
        $grid->column('note', __('Note'));
        $grid->column('function', __('Function'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(PhysicalConfig::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('weight','权重');
        $show->field('description', __('Description'));
        $show->field('note', __('Note'));
        $show->field('function', __('Function'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PhysicalConfig);

        $form->text('name', __('Name'));
        $form->decimal('weight', '权重');
        $form->text('description', __('Description'));
        $form->text('note', __('Note'));
        $form->text('function', __('Function'));

        return $form;
    }
}
