<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\CoolerDetail;
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
            ->header('冰箱属性')
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
        $grid->cooler_id('Cooler id');
        $grid->cooler()->cooler_name('冰箱名称');
        $grid->sys()->name('标题');
        $grid->value('属性');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

        $grid->filter(function ($filter){
            /* @var $filter Grid\Filter */
            $filter->disableIdFilter();
            $filter->equal('cooler_id','设备id');
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
        $show = new Show(CoolerDetail::findOrFail($id));

        $show->id('Id');
        $show->cooler_id('Cooler id');
        $show->company_id('Company id');
        $show->sys_cooler_detail_id('Sys cooler detail id');
        $show->value('Value');
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

        $form->number('cooler_id', 'Cooler id');
        $form->number('company_id', 'Company id');
        $form->number('sys_cooler_detail_id', 'Sys cooler detail id');
        $form->text('value', 'Value');
        $form->datetime('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->datetime('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
