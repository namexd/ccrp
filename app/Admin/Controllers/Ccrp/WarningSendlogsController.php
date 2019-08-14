<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\ExcelExpoter;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\WarningSendlog;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WarningSendlogsController extends Controller
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

        $grid = new Grid(new WarningSendlog);

//        $company = Company::where('username','441900000000')->first();
//        $grid->model()->whereIn('company_id',$company->ids());
//        $grid->model()->whereBetween('send_time',[strtotime('2017-03-01'),strtotime('2017-04-01')]);
//        $grid->model()->whereIn('event_type',[WarningSendlog::温度报警,WarningSendlog::市电断电]);
        $grid->id('Id');
        $grid->company()->title('单位名称');
//        $grid->cooler_name('Cooler name');
//        $grid->collector_name('Collector name');
        $grid->event_type('预警类型');
        $grid->event_level('预警级别');
        $grid->send_to('发送至');
        $grid->send_time('发送时间');
        $grid->send_content_all('内容');
        $grid->send_status('发送状态');


        $grid->filter(function ($filter) {
            $filter->like('title', '单位名称');
            $filter->like('short_title', '单位简称');
            $filter->equal('phone', '手机');
            $filter->equal('username', '用户名');
            $filter->equal('pid', '上级ID');

            $filter->equal('event_type', '预警类型')->checkbox(WarningSendlog::EVENT_TYPES);

//            $filter->where(function ($query) {
//                switch ($this->input) {
//                }
//            }, '报警通道')->datetime(['format' => 'YYYY-MM-DD']);

            $filter->between('send_time','发送时间')->datetime();

        });

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableRowSelector();

        $grid->exporter($this->excel());

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
        $show = new Show(WarningSendlog::findOrFail($id));

        $show->id('Id');
        $show->event_id('Event id');
        $show->event_type('Event type');
        $show->event_value('Event value');
        $show->event_level('Event level');
        $show->msg_type('Msg type');
        $show->send_to('Send to');
        $show->send_time('Send time');
        $show->send_content('Send content');
        $show->send_content_all('Send content all');
        $show->send_status('Send status');
        $show->send_rst('Send rst');
        $show->collector_id('Collector id');
        $show->collector_name('Collector name');
        $show->cooler_id('Cooler id');
        $show->cooler_name('Cooler name');
        $show->sender_id('Sender id');
        $show->sent_again('Sent again');
        $show->category_id('Category id');
        $show->company_id('Company id');
        $show->from_source('From source');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WarningSendlog);

        $form->number('event_id', 'Event id');
        $form->text('event_type', 'Event type');
        $form->decimal('event_value', 'Event value');
        $form->text('event_level', 'Event level');
        $form->number('msg_type', 'Msg type');
        $form->text('send_to', 'Send to');
        $form->number('send_time', 'Send time');
        $form->text('send_content', 'Send content');
        $form->text('send_content_all', 'Send content all');
        $form->number('send_status', 'Send status');
        $form->text('send_rst', 'Send rst');
        $form->number('collector_id', 'Collector id');
        $form->text('collector_name', 'Collector name');
        $form->number('cooler_id', 'Cooler id');
        $form->text('cooler_name', 'Cooler name');
        $form->text('sender_id', 'Sender id');
        $form->number('sent_again', 'Sent again');
        $form->number('category_id', 'Category id');
        $form->number('company_id', 'Company id');
        $form->text('from_source', 'From source');

        return $form;
    }

    private function excel()
    {
        $excel = new ExcelExpoter();
        $excel->setFileName('预警发送 记录');
        $columns = [
            'id' => 'ID',
            'company.title' => '单位名称',
            'event_type' => '预警类型',
            'event_level' => '预警级别',
            'send_to' => '发送至',
            'send_time' => '发送时间',
            'send_content_all' => '内容',
        ];
        $excel->setColumn($columns);
        $excel->setColumnFormat([
            'send_to' => ExcelExpoter::单元格格式字符,
        ]);
        return $excel;
    }
}
