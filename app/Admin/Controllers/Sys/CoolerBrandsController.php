<?php

namespace App\Admin\Controllers\Sys;

use App\Models\Ccrp\Sys\SysCoolerBrand;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CoolerBrandsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Ccrp\Sys\SysCoolerBrand';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SysCoolerBrand);
        $grid->model()->orderBy('popularity','desc');
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('slug', __('Slug'));
        $grid->column('comporation', __('Comporation'));
        $grid->column('has_medical', __('Has medical'));
        $grid->column('popularity', __('Popularity'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->filter(function ($filter) {
//            $filter->disableIdFilter();
            $filter->equal('name', '品牌名称');
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
        $show = new Show(SysCoolerBrand::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('slug', __('Slug'));
        $show->field('comporation', __('Comporation'));
        $show->field('has_medical', __('Has medical'));
        $show->field('popularity', __('Popularity'));
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
        $form = new Form(new SysCoolerBrand);

        $form->text('name', __('Name'));
        $form->text('slug', __('Slug'));
        $form->text('comporation', __('Comporation'));
        $form->switch('has_medical', __('Has medical'));
        $form->number('popularity', __('Popularity'));

        return $form;
    }
}
