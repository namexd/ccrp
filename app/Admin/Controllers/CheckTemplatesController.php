<?php

namespace App\Admin\Controllers;

use App\Models\Ccrp\CheckTemplate;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CheckTemplatesController extends Controller
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
        $grid = new Grid(new CheckTemplate);

        $grid->id('Id');
        $grid->title('模板名称');
        $grid->content('Content');
        $grid->type('模板类型');
        $grid->cycle_type('周期类型');
        $grid->status('状态')->switch();
        $grid->version('Version');
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
        $show = new Show(CheckTemplate::findOrFail($id));

        $show->id('Id');
        $show->title('模板名称');
        $show->content('Content');
        $show->type('模板类型');
        $show->cycle_type('周期类型');
        $show->status('Status');
        $show->version('Version');
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
        $form = new Form(new CheckTemplate);

        $form->text('title', '模板名称');
        $form->text('content', 'Content');
        $form->text('type', '模板类型')->default('html');
        $form->select('cycle_type', '周期类型')->options(CheckTemplate::CYCLE_TYPE);
        $form->dateRange('start','end','自定义时间');
        $form->switch('status', 'Status')->default(1);
        $form->text('version', 'Version');

        return $form;
    }
}
