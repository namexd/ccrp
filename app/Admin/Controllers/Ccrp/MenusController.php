<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Menu;
use App\Models\Ccrp\Role;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;

class MenusController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Ccrp\Menu';

    public function index(Content $content)
    {
        return $content
            ->header('用户菜单')
            ->description('列表')
            ->row(function (Row $row) {
//                $row->column(6,Menu::tree());
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action('menus');

                    $menuModel = Menu::class;

                    $form->radio('category', '标识')->options(['PC' => 'PC', 'Weixin' => 'Weixin']);
                    $form->select('pid', trans('admin.parent_id'))->options($menuModel::selectOptions());
                    $form->text('title', trans('admin.title'))->rules('required');

                    $form->text('slug', '标识')->rules('required');
                    $form->textarea('comment', '备注');
//                    $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));

                    $form->hidden('_token')->default(csrf_token());


                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
    }


    protected function treeView()
    {

        $menuModel = Menu::class;

        return $menuModel::tree(function ($tree) {
            $tree->disableCreate();

            $tree->branch(function ($branch) {
                $payload = "&nbsp;<strong>{$branch['title']}</strong>";

                if (!isset($branch['children'])) {

                    $payload .= "<br>  【{$branch['id']}】 <span  class=\"dd-nodrag\">{$branch['slug']}</span>";
                }

                return $payload;
            });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Menu);

        $grid->column('id', __('Id'));
        $grid->column('category', __('Category'));
        $grid->column('position', __('Position'));
        $grid->column('pid', __('Pid'));
        $grid->column('title', __('Title'));
        $grid->column('slug', __('Slug'));
        $grid->column('aliastitle', __('Aliastitle'));
        $grid->column('url', __('Url'));
        $grid->column('icon', __('Icon'));
        $grid->column('dev', __('Dev'));
        $grid->column('fee', __('Fee'));
        $grid->column('ctime', __('Ctime'));
        $grid->column('utime', __('Utime'));
        $grid->column('comment', __('Comment'));
        $grid->column('sort', __('Sort'));
        $grid->column('status', __('Status'));

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
        $show = new Show(Menu::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category', __('Category'));
        $show->field('position', __('Position'));
        $show->field('pid', __('Pid'));
        $show->field('title', __('Title'));
        $show->field('slug', __('Slug'));
        $show->field('aliastitle', __('Aliastitle'));
        $show->field('url', __('Url'));
        $show->field('icon', __('Icon'));
        $show->field('dev', __('Dev'));
        $show->field('fee', __('Fee'));
        $show->field('ctime', __('Ctime'));
        $show->field('utime', __('Utime'));
        $show->field('comment', __('Comment'));
        $show->field('order', __('Sort'));
        $show->field('status', __('Status'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Menu);

        $form->text('category', __('Category'));
        $form->text('position', __('Position'))->default('LEFT');
        $form->number('pid', __('Pid'));
        $form->text('title', __('Title'));
        $form->text('slug', __('Slug'));
        $form->text('aliastitle', __('Aliastitle'));
        $form->url('url', __('Url'));
        $form->text('icon', __('Icon'));
        $form->switch('dev', __('Dev'));
        $form->number('fee', __('Fee'));
        $form->number('ctime', __('Ctime'));
        $form->number('utime', __('Utime'));
        $form->text('comment', __('Comment'));
        $form->text('order', __('Sort'));
        $form->switch('status', __('Status'))->default(1);
        $form->multipleSelect('roles','单位类型')->options(Role::all()->pluck('name','id'));
        return $form;
    }
}
