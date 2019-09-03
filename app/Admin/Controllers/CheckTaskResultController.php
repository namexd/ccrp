<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Check\ResetCheck;
use App\Models\Ccrp\CheckTaskResult;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CheckTaskResultController extends Controller
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
            ->header('计算结果')
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
        $grid = new Grid(new CheckTaskResult);
        $grid->disableCreateButton();
        $grid->filter(function ($filter){
            $filter->expand();
            $filter->like('key','key');
            $filter->equal('status','状态')->radio(['1'=>'已完成','0'=>'未完成']);
        });
        $grid->id('Id');
        $grid->task_id('Task id');
        $grid->key('Key');
        $grid->status('状态')->switch();;
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->batchActions(function ($batch) {
            $batch->add(new ResetCheck());
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
        $show = new Show(CheckTaskResult::findOrFail($id));

        $show->id('Id');
        $show->task_id('Task id');
        $show->key('Key');
        $show->value('Value');
        $show->status('Status');
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
        $form = new Form(new CheckTaskResult);

        $form->number('task_id', 'Task id');
        $form->text('key', 'Key');
        $form->textarea('value', 'Value');
        $form->switch('status', 'Status');

        return $form;
    }

    public function reset()
    {
        foreach (CheckTaskResult::find(request()->get('ids')) as $value) {
            $value->status = request()->get('action');
            $value->save();
        }
    }
}
