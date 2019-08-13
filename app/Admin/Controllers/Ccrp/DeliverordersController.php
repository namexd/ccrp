<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Deliverorder;
use App\Models\Ccrp\Printerlog;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class DeliverordersController extends Controller
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
            ->header('便携式订单')
            ->description('Deliverorder')
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
        $grid = new Grid(new Deliverorder());

        $grid->model()->orderBy('deliverorder_id','desc');
        $grid->deliverorder_id('Id');
        $grid->company()->title('单位');
        $grid->deliverorder('订单编号');
        $grid->customer_name('客户名称');
        $grid->delivervehicle('派送车辆');
        $grid->deliver('派送人');
        $grid->create_time('订单时间')->display(function($value){
            return date('Y-m-d H:i',$value);
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
        $show = new Show(Deliverorder::findOrFail($id));

        $show->deliverorder_id('Id');
        $show->company()->title('单位');
        $show->deliverorder('订单编号');
        $show->customer_name('客户名称');
        $show->delivervehicle('派送车辆');
        $show->deliver('派送人');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Deliverorder);

        $form->text('customer_name','客户名称');
        $form->text('delivervehicle','派送车辆');
        $form->text('deliver','派送人');

        return $form;
    }
}
