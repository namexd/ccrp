<?php

namespace App\Admin\Controllers\Ccrp\Sys;

use App\Models\Ccrp\Sys\CoolerDetail;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CoolerDetailsController extends Controller
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
            ->header('冰箱系统信息')
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
        $grid = new Grid(new CoolerDetail);

        $grid->id('Id');
        $grid->category('分类');
        $grid->name('名称');
        $grid->slug('Slug');
        $grid->value('Value');
        $grid->description('Description');
        $grid->note('Note');
        $grid->sort('Sort');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(CoolerDetail::findOrFail($id));

        $show->id('Id');
        $show->category('Category');
        $show->name('Name');
        $show->slug('Slug');
        $show->value('Value');
        $show->description('Description');
        $show->note('Note');
        $show->sort('Sort');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CoolerDetail);

        $form->radio('category', '分类')->options(CoolerDetail::CATEGORIES);
        $form->text('name', '名称');
        $form->text('slug', '标识');
        $form->text('value', '默认值');
        $form->text('description', '描述');
        $form->text('note', '备注');
        $form->number('sort', '排序')->default(1);

        return $form;
    }
}
