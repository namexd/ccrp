<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Employee;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EmployeesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Ccrp\Employee';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Employee);

        $grid->column('id', __('Id'));
        $grid->column('username', __('Username'));
        $grid->column('password', __('Password'));
        $grid->column('category', __('Category'))->using(Employee::CATEGORIES);
        $grid->column('note', __('Note'));
        $grid->column('userlevel', __('Userlevel'))->using(Employee::USERLEVELS);
//        $grid->column('realname', __('Realname'));
        $grid->column('phone', __('Phone'));
        $grid->column('menu_company', __('查询单位账号菜单'))->using(['0'=>'无','1'=>'有']);
//        $grid->column('status', __('Status'))->switch();

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
        $show = new Show(Employee::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('username', __('Username'));
        $show->field('password', __('Password'));
        $show->field('category', __('Category'));
        $show->field('note', __('Note'));
        $show->field('usertype', __('Usertype'));
        $show->field('userlevel', __('Userlevel'));
        $show->field('company', __('Company'));
        $show->field('company_id', __('Company id'));
        $show->field('email', __('Email'));
        $show->field('mobile', __('Mobile'));
        $show->field('group', __('Group'));
        $show->field('avatar', __('Avatar'));
        $show->field('score', __('Score'));
        $show->field('money', __('Money'));
        $show->field('sex', __('Sex'));
        $show->field('age', __('Age'));
        $show->field('birthday', __('Birthday'));
        $show->field('summary', __('Summary'));
        $show->field('realname', __('Realname'));
        $show->field('idcard_no', __('Idcard no'));
        $show->field('login', __('Login'));
        $show->field('last_login_time', __('Last login time'));
        $show->field('last_login_ip', __('Last login ip'));
        $show->field('ctime', __('Ctime'));
        $show->field('utime', __('Utime'));
        $show->field('sort', __('Sort'));
        $show->field('status', __('Status'));
        $show->field('phone', __('Phone'));
        $show->field('menu_company', __('Menu company'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Employee);

        $form->text('username', __('Username'));
        $form->text('password', __('Password'));
        $form->radio('category', __('Category'))->options(Employee::CATEGORIES);
        $form->text('note', __('Note'));
//        $form->switch('usertype', __('Usertype'));
        $form->radio('userlevel', __('Userlevel'))->options(Employee::USERLEVELS);
//        $form->text('company', __('Company'));
//        $form->number('company_id', __('Company id'));
//        $form->email('email', __('Email'));
//        $form->mobile('mobile', __('Mobile'));
//        $form->number('group', __('Group'));
//        $form->number('avatar', __('Avatar'));
//        $form->number('score', __('Score'));
//        $form->decimal('money', __('Money'))->default(0.00);
//        $form->text('sex', __('Sex'));
//        $form->number('age', __('Age'));
//        $form->number('birthday', __('Birthday'));
//        $form->text('summary', __('Summary'));
        $form->text('realname', __('Realname'));
//        $form->text('idcard_no', __('Idcard no'));
//        $form->number('login', __('Login'));
//        $form->number('last_login_time', __('Last login time'));
//        $form->text('last_login_ip', __('Last login ip'));
//        $form->number('ctime', __('Ctime'));
//        $form->number('utime', __('Utime'));
//        $form->switch('sort', __('Sort'));
//        $form->switch('status', __('Status'));
        $form->mobile('phone', __('Phone'));
        $form->switch('menu_company', __('Menu company'));

        return $form;
    }
}
