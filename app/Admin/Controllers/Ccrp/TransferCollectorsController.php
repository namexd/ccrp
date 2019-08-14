<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\TransferCollector;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TransferCollectorsController extends Controller
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
        $grid = new Grid(new TransferCollector);

        $grid->id('Id');
        $grid->transfer()->name('转发路由');
        $grid->company()->title('单位名称');
        $grid->cooler()->cooler_name('设备名称');
        $grid->collector()->supplier_collector_id('探头编码');
        $grid->trans_collect_time('采集时间');
        $grid->trans_times('传输次数');
        $grid->trans_err_times('传输错误');
        $grid->trans_data('传输数据');
        $grid->status('当前状态')->switch();
        $grid->trans_data_id('数据id');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');
        $grid->filter(function ($filter) {
            $filter->expand();
            $filter->equal('status', '状态')->radio([
                '' => '所有',
                '0' => '否',
                '1' => '是',
            ]);
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
        $show = new Show(TransferCollector::findOrFail($id));

        $show->id('Id');
        $show->transfer_id('Transfer id');
        $show->company_id('Company id');
        $show->cooler_id('Cooler id');
        $show->collector_id('Collector id');
        $show->trans_data_id('Trans data id');
        $show->trans_collect_time('Trans collect time');
        $show->trans_times('Trans times');
        $show->trans_err_times('Trans err times');
        $show->trans_data('Trans data');
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
        $form = new Form(new TransferCollector);

        $form->number('transfer_id', 'Transfer id');
        $form->number('company_id', 'Company id');
        $form->number('cooler_id', 'Cooler id');
        $form->number('collector_id', 'Collector id');
        $form->number('trans_data_id', 'Trans data id');
        $form->datetime('trans_collect_time', 'Trans collect time')->default(date('Y-m-d H:i:s'));
        $form->number('trans_times', 'Trans times');
        $form->number('trans_err_times', 'Trans err times');
        $form->number('trans_data', 'Trans data');
        $form->switch('status', 'Status')->default(1);

        return $form;
    }
}
