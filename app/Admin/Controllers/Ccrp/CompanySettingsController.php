<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Actions\CompanySetting\Check;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\CompanyHasSetting;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\Sys\Setting;
use App\Models\Ccrp\Tag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CompanySettingsController extends AdminController
{
    use HasResourceActions;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CompanyHasSetting);

        $grid->id('Id');
        $grid->model()->orderBy('company_id', 'asc');
        $grid->company()->title('单位名称');
        $grid->setting()->name('设置名称');
        $grid->value('设置值')->display(function ($value) {
            if($option = $this->setting->options)
            {
                $options = json_decode($option,true);
                if($value==$this->setting->value)
                {
                    return '<span class="label label-success">' . $options[$value] . '</span>';
                }else{
                    return '<span class="label label-danger">' . $options[$value] . '</span>';
                }
            }else{
                if($value==$this->setting->value) {
                    return '<span class="label label-success">' . $value . '</span>';
                }else{
                    return '<span class="label label-danger">' . $value . '</span>';
                }
            }
        });
        $grid->setting()->value("默认值")->display(function ($value) {
            if($option = $this->setting->options)
            {
                $options = json_decode($option,true);
                return $options[$value] ;
            }else{
                return   $value ;
            }
        });
        $grid->setting()->tip('设置说明');
        $grid->setting()->options('设置说明')->display(function($value){
            return $value;
        });
        $grid->created_at('设置时间');
        $grid->updated_at('修改时间');

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->add(new Check());
        });


        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
//            $filter->equal('company_id', '单位id');


            $filter->equal('company_id', '单位')->select(
                Company::whereIn('id', CompanyHasSetting::pluck('company_id'))->orderBy('title','asc')->pluck('title', 'id')
            );
            $filter->equal('setting_id', '设置项')->radio(
                Setting::all()->where('status',1)->pluck('name', 'id')
            );
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
        $show = new Show(CompanyHasSetting::findOrFail($id));

        $show->id('Id');
        $show->setting(function($setting){
            $setting->name('设置名称');
        });
        $show->company(function($setting){
            $setting->title('单位名称');
        });
        $show->value('设置值');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CompanyHasSetting);

        $form->select('setting_id', '设置')
            ->options(Setting::orderBy('name', 'ASC')->where('status',1)->pluck('name', 'id'));
        $form->select('company_id', '单位')
            ->options(Company::whereHas('tags', function ($query) {
                $query->where('tags.id', Tag::自定义设置)->orderBy('title', 'asc');
            })->pluck('title', 'id'));
        $form->text('value', '设置值');
//        $form->datetime('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
//        $form->datetime('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
