<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Printerlog;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PrinterlogsController extends Controller
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
        $grid = new Grid(new Printerlog);

        $grid->model()->orderBy('id','desc');
        $grid->id('Id');
        $grid->company()->title('单位');
        $grid->printer_id('Printer id');
        $grid->title('Title');
        $grid->subtitle('Subtitle');
        $grid->print_time('Print time');
        $grid->company_id('Company id');
        $grid->uid('Uid');
        $grid->pages('Pages');
        $grid->pagei('Pagei');
        $grid->sign_id('Sign id');
        $grid->sign_time('Sign time');

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
        $show = new Show(Printerlog::findOrFail($id));

        $show->id('Id');
        $show->printer_id('Printer id');
        $show->title('Title');
        $show->subtitle('Subtitle');
        $show->content('Content');
        $show->print_time('Print time');
        $show->company_id('Company id');
        $show->uid('Uid');
        $show->orderindex('Orderindex');
        $show->server_state('Server state');
        $show->order_state('Order state');
        $show->order_status('Order status');
        $show->pages('Pages');
        $show->pagei('Pagei');
        $show->from_type('From type');
        $show->from_device('From device');
        $show->from_order_id('From order id');
        $show->from_time_begin('From time begin');
        $show->from_time_end('From time end');
        $show->sign_id('Sign id');
        $show->sign_time('Sign time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Printerlog);

//        $form->number('printer_id', 'Printer id');
        $form->text('title', 'Title');
        $form->text('subtitle', 'Subtitle');
        $form->textarea('content', 'Content');
//        $form->number('print_time', 'Print time');
//        $form->number('company_id', 'Company id');
//        $form->number('uid', 'Uid');
//        $form->text('orderindex', 'Orderindex');
//        $form->text('server_state', 'Server state');
//        $form->switch('order_state', 'Order state');
//        $form->text('order_status', 'Order status');
//        $form->number('pages', 'Pages')->default(1);
//        $form->number('pagei', 'Pagei')->default(1);
//        $form->text('from_type', 'From type');
//        $form->text('from_device', 'From device');
//        $form->text('from_order_id', 'From order id');
//        $form->number('from_time_begin', 'From time begin');
//        $form->number('from_time_end', 'From time end');
//        $form->number('sign_id', 'Sign id');
//        $form->number('sign_time', 'Sign time');

        return $form;
    }
}
