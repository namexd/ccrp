<?php

namespace App\Admin\Controllers\Ccrp\Sys;

use App\Http\Controllers\Controller;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sys\Setting;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SettingsController extends Controller
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
            ->header('全局默认配置项')
            ->description('系统默认值')
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
        $grid = new Grid(new Setting);

        $grid->model()->orderBy('sort','asc')->orderBy('id','asc');
        $grid->id('Id');
        $grid->category('分类（表名）')->using(Setting::CATEGORIES);
        $grid->name('名称');
        $grid->slug('标识');
        $grid->value('默认值');
        $grid->group('分组')->using(Setting::GROUPS);
        $grid->type('类型')->using(Setting::TYPES);
        $grid->options('选项');
        $grid->tip('提示');
        $grid->sort('排序');
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

        $grid->filter(function ($filter) {
            $filter->equal('category', '类别')->radio(Setting::CATEGORIES);
            $filter->equal('type', '类型')->radio(Setting::TYPES);
            $filter->equal('type', '组别')->radio(Setting::GROUPS);
            $filter->equal('name', '名称');
            $filter->equal('slug', '标识');
            $filter->equal('status', '状态')->radio(['0'=>'禁用','1'=>'启用']);
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
        $show = new Show(Setting::findOrFail($id));

        $show->id('Id');
        $show->category('Category');
        $show->name('Name');
        $show->slug('Slug');
        $show->value('Value');
        $show->group('Group');
        $show->type('Type');
        $show->options('Options');
        $show->tip('Tip');
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
        $form = new Form(new Setting);

        $form->radio('category', '分类')->options(Setting::CATEGORIES)->default('company');
        $form->text('name', '名称');
        $form->text('slug', '标识');
        $form->text('value', '默认值');
        $form->radio('group', '分组')->options(Setting::GROUPS);
        $form->radio('type', '数值类型')->options(Setting::TYPES)->default('text');
        $form->text('tip', '提示');
        $form->textarea('options', '选项（扩展）');
        $form->text('check_route', '检查的路由');
        $form->text('set_route', '设置的路由');
        $form->number('sort', '排序')->default(1);
        $form->switch('status', '状态')->default(1);

        return $form;
    }

    public function company(Company $company, Content $content)
    {
        $grid = new Grid(new Setting);
        $grid->id('Id');
        $grid->company('单位名称')->display(function () use ($company) {
            return $company->title;
        });
        $grid->name('名称');
        $grid->slug('标识');
        $grid->diy_value('设定值')->display(function () use ($company) {
            $diy = $company->getHasSettings($this->id);
            if ($diy) {
                return '<span class="label label-danger">' . ($diy->value) . '</span>';
            } else {
                return $this->value;
            }
        });
        $grid->value('默认值');
        $grid->group('组别')->using(Setting::GROUPS);
        $grid->type('类型')->using(Setting::TYPES);
        $grid->options('选项');
        $grid->tip('提示');
        $grid->sort('排序');
        $grid->status('状态')->using(Setting::STATUSES);
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->actions(function ($actions) use ($company) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

            $actions->append('<a target="_blank" href="' . route($actions->row->check_route, [$this->row->id, $company->id]) . '"><i class="fa fa-eye" title="检查"></i></a>');
        });
        return $content
            ->header($company->title . ' 单位设置')
            ->description('疾控默认设置，比如离线时间等')
            ->body($grid);
    }

    public function default(Company $company, Content $content)
    {
        $grid = new Grid(new Setting);
        $grid->id('Id');
        $grid->company('单位名称')->display(function () use ($company) {
            return $company->title;
        });
        $grid->name('名称');
        $grid->slug('标识');
        $grid->diy_value('设定值')->display(function () use ($company) {
            $diy = $company->getHasSettings($this->id);
            if ($diy) {
                return '<span class="label label-danger">' . ($diy->value) . '</span>';
            } else {
                return $this->value;
            }
        });
        $grid->value('默认值');
        $grid->group('组别')->using(Setting::GROUPS);
        $grid->type('类型')->using(Setting::TYPES);
        $grid->options('选项');
        $grid->tip('提示');
        $grid->sort('排序');
        $grid->status('状态')->using(Setting::STATUSES);
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->actions(function ($actions) use ($company) {
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();

            $actions->append('<a target="_blank" href="' . route($actions->row->check_route, [$this->row->id, $company->id]) . '"><i class="fa fa-eye" title="检查"></i></a>');
        });
        return $content
            ->header($company->title . ' 单位设置')
            ->description('疾控默认设置，比如离线时间等')
            ->body($grid);
    }

    public function check(Setting $setting, $object_value, Content $content)
    {
        $result = $setting->checkObject($object_value);
        return $result;
    }

}
