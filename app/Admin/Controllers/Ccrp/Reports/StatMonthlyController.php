<?php

namespace App\Admin\Controllers\Ccrp\Reports;

use App\Models\Ccrp\Reports\StatMonthly;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class StatMonthlyController extends Controller
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
            ->header('人工测温记录')
            ->description('谨慎操作')
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
        $grid = new Grid(new StatMonthly);

        $grid->id('Id');
        $grid->company_id('单位id');
        $grid->year('年');
        $grid->month('月');
        $grid->day('日');
        $grid->sign_time_a('上午下午');
        $grid->cooler_id('冰箱 id');
        $grid->cooler_name('冰箱名称');
        $grid->cooler_sn('冰箱SN');
        $grid->temp_cool('冷藏温度');
        $grid->temp_cold('冷冻温度');
        $grid->sign_note('签名备注');
        $grid->sign_id('签名id');
        $grid->sign_time('签名时间')->display(function($value){
            return date('Y-m-d H:i',$value);
        });
        $grid->create_time('创建时间')->display(function($value){
            return date('Y-m-d H:i',$value);
        });

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('company_id', '单位ID');
            $filter->equal('year', '年')->default(date('Y'));
            $filter->equal('month', '月')->default(date('m'));
            $filter->equal('day', '日')->default(date('d'));
            $filter->equal('sign_time_a', '上午下午')->radio(['AM'=>'上午','PM'=>'下午']);
        });
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools)   {
            $tools->batch(function ($batch)  {
                $batch->disableDelete(false);
            });
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
        $show = new Show(StatMonthly::findOrFail($id));

        $show->id('Id');
        $show->company_id('Company id');
        $show->year('Year');
        $show->month('Month');
        $show->day('Day');
        $show->cooler_id('Cooler id');
        $show->cooler_name('Cooler name');
        $show->cooler_sn('Cooler sn');
        $show->cooler_type('Cooler type');
        $show->temp_cool('Temp cool');
        $show->temp_cold('Temp cold');
        $show->sign_note('Sign note');
        $show->sign_id('Sign id');
        $show->sign_time('Sign time');
        $show->sign_time_a('Sign time a');
        $show->create_time('Create time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StatMonthly);

        $form->number('company_id', 'Company id');
        $form->number('year', 'Year');
        $form->number('month', 'Month');
        $form->number('day', 'Day')->default(1);
        $form->number('cooler_id', 'Cooler id');
        $form->text('cooler_name', 'Cooler name');
        $form->text('cooler_sn', 'Cooler sn');
        $form->number('cooler_type', 'Cooler type');
        $form->text('temp_cool', 'Temp cool');
        $form->text('temp_cold', 'Temp cold');
        $form->text('sign_note', 'Sign note');
        $form->number('sign_id', 'Sign id');
        $form->number('sign_time', 'Sign time');
        $form->text('sign_time_a', 'Sign time a')->default('AM');
        $form->number('create_time', 'Create time');

        return $form;
    }
}
