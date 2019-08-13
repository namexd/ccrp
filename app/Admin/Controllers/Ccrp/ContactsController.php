<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Contact;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ContactsController extends Controller
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
            ->header('预警联系人')
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
        $grid = new Grid(new Contact);

        $grid->model()->orderBy('contact_id','desc');
        $grid->contact_id('Id');
        $grid->company()->title('单位名称');
        $grid->name('名称');
        $grid->phone('电话');
        $grid->job('职位');
        $grid->note('备注');
        $grid->level('级别');
        $grid->create_time('创建时间')->display(function ($value){
            return Carbon::createFromTimestamp($value)->toDateTimeString();
        });
        $grid->status('状态')->switch();

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->equal('company_id', '单位id');
            $filter->like('name', '名称');
            $filter->like('phone', '手机号');
            $filter->equal('status', '状态')->radio(['禁用','启用']);

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
        $show = new Show(Contact::findOrFail($id));

        $show->contact_id('Contact id');
        $show->name('Name');
        $show->phone('Phone');
        $show->email('Email');
        $show->job('Job');
        $show->voice('Voice');
        $show->note('Note');
        $show->level('Level');
        $show->company_id('Company id');
        $show->create_uid('Create uid');
        $show->create_time('Create time');
        $show->status('Status');
        $show->category_id('Category id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Contact);

        $form->text('name', 'Name');
        $form->mobile('phone', 'Phone');
        $form->email('email', 'Email');
        $form->text('job', 'Job');
        $form->switch('voice', 'Voice');
        $form->text('note', 'Note');
        $form->switch('level', 'Level')->default(1);
        $form->number('company_id', 'Company id');
        $form->number('create_uid', 'Create uid');
        $form->number('create_time', 'Create time');
        $form->switch('status', 'Status')->default(1);
        $form->number('category_id', 'Category id');

        return $form;
    }
}
