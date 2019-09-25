<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\File;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FilesController extends Controller
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
            ->header('证书文件')
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
        $grid = new Grid(new File);

        $grid->id('Id');
        $grid->file_name('探头名称');
//        $grid->file_server('存储服务器');
//        $grid->file_url('文件地址');
        $grid->file_type('文件类型');
        $grid->file_category('文件分类');
        $grid->file_desc('文件描述');
//        $grid->company_id('单位id');
        $grid->company_name('单位名称');
        $grid->create_time('创建时间')->display(function($item){
            return date('Y-m-d',$item);
        });
//        $grid->out_date('过期时间');
//        $grid->file_url2('File url2');
        $grid->status('状态')->switch();
        $grid->note('备注');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
                $filter->equal('company', '单位名称');
//            $filter->like('short_title', '单位简称');
                $filter->equal('file_name', '探头名称');

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
        $show = new Show(File::findOrFail($id));

        $show->id('Id');
        $show->file_name('File name');
        $show->file_server('File server');
        $show->file_url('File url');
        $show->file_type('File type');
        $show->file_category('File category');
        $show->file_desc('File desc');
        $show->company_id('Company id');
        $show->company_name('Company name');
        $show->create_time('Create time');
        $show->out_date('Out date');
        $show->file_url2('File url2');
        $show->status('Status');
        $show->note('Note');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new File);

        $form->text('file_name', 'File name');
        $form->text('file_server', 'File server')->default('https://oss.coldyun.net/');
        $form->text('file_url', 'File url')->default('/www/web/we_coldyun_net/ccrp/certifications/');
        $form->text('file_type', 'File type')->default('.jpg');
        $form->select('file_category', 'File category')->options(File::CATEGORIES)->default('第三方校准证书');
        $form->text('file_desc', 'File desc')->default('('.(date('Y')-1).'年) - ');
        $form->number('company_id', 'Company id');
        $form->text('company_name', 'Company name');
        $form->hidden('create_time', 'Create time')->default(time());
        $form->date('out_date', 'Out date')->default(date('Y-m-d',time()+365*3600*24));
        $form->text('file_url2', 'File url2');
        $form->switch('status', 'Status')->default(1);
        $form->text('note', 'Note');

        return $form;
    }
}
