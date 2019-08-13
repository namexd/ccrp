<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\Tools\UpdateField;
use App\Admin\Extensions\Tools\UpdateRow;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\CompanyFunction;
use App\Models\Ccrp\CompanyHasFunction;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\Sys\Setting;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class CompanyHasFunctionsController extends Controller
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
        $grid = new Grid(new CompanyHasFunction);

        $grid->id('Id');
        $grid->company_id('Company id');
        $grid->function_id('Function id');
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
        $show = new Show(CompanyHasFunction::findOrFail($id));

        $show->id('Id');
        $show->company_id('Company id');
        $show->function_id('Function id');
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
        $form = new Form(new CompanyHasFunction);

        $form->number('company_id', 'Company id');
        $form->number('function_id', 'Function id');
        $form->datetime('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->datetime('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $form;
    }


    public function updateRows(Request $request)
    {
        $function_slug = $request->get('function');

        $function = CompanyFunction::where('slug', $function_slug)->first();
        $oprate = $request->get('oprate');
        //增加
        if ($oprate == 1) {

            foreach ($request->get('ids') as $item) {
                $hasFunction = CompanyHasFunction::where('function_id', $function->id)->where('company_id', $item)->first();
                if (!$hasFunction) {
                    $hasFunction = new CompanyHasFunction();
                    $hasFunction->company_id = $item;
                    $hasFunction->function_id = $function->id;
                    $hasFunction->save();
                }
            }
        } else {

            foreach ($request->get('ids') as $item) {
                $hasFunction = CompanyHasFunction::where('function_id', $function->id)->where('company_id', $item)->first();
                $hasFunction->delete();
            }
        }
    }


    public function checkManualRecords(Setting $setting, Company $company, Content $content)
    {
        $function = CompanyFunction::find(CompanyFunction::人工签名ID);
        return $this->checkFunction($function, $setting, $company, $content);
    }

    public function checkFunction(CompanyFunction $function, Setting $setting, Company $company, Content $content)
    {
        $diy = $company->getHasSettings($setting->id);
        $value = $diy->value ?? $setting->value;
        $grid = new Grid(new Company());

        if ($value == 1) {
            $grid->model()->whereIn('id', $company->ids())->where('status', Company::状态正常)->doesntHave('functionManualRecords');
        } else {
            $grid->model()->whereIn('id', $company->ids())->where('status', Company::状态正常)->whereHas('functionManualRecords');
        }
        $grid->id('Id');
        $grid->area()->merger_name('地区');
        $grid->parent()->title('上级单位');
        $grid->title('单位名称');
        $grid->username('登录名');
        $grid->address('地址');

        $grid->disableCreateButton();

        $grid->disableActions();
//        $grid->disableRowSelector();
        $grid->tools(function ($tools) use ($value, $function, $setting) {
            $tools->batch(function ($batch) use ($value, $function, $setting) {
                $batch->disableDelete();

                $batch->add(
                    (($value == 1) ? "开启" : "关闭") . ' 【' . $function->name . '】 ' . '',
                    new UpdateRow(route($setting->set_route), $function->slug, $value)
                );
            });
        });

//        $grid->exporter($this->excel());
        return $content
            ->header($company->title . ' 【' . $function->name . '】的功能：' . ($value == 1 ? "未开启" : "开启") . '的')
            ->description('不含暂停的单位')
            ->body($grid);

    }

}
