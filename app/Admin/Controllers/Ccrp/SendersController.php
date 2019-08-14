<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sender;
use App\Http\Controllers\Controller;
use function app\Utils\format_time;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;
use Illuminate\Support\Facades\Input;

class SendersController extends AdminController
{
    use HasResourceActions;


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Sender);

        $grid->model()->where('status',1)->orderBy('id', 'desc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->category()->title('单位分类');
        $grid->sender_id('主机编号');
        $grid->supplier_model('型号');
//        $grid->supplier_id('Supplier id');
//        $grid->company_id('Company id');
        $grid->status('状态')->using(
            [
                2=>'报废',
                1=>'正常',
                0=>'禁用'
            ]
        );
        $grid->note('Note');
        $grid->note2('Note2');
        $grid->simcard('sim卡号');
        $grid->ischarging('是否插电')->using(
            [
                1=>'是',
                0=>'断电'
            ]
        );
        $grid->ischarging_update_time('市电更新时间')->display(function($value){
            return format_time($value);
        });
//        $grid->install_uid('Install uid');

        $grid->install_time('安装时间')->display(function($value){
            return format_time($value);
        });
        $grid->uninstall_time('报废时间')->display(function($value){
            return format_time($value);
        });
        $grid->update_time('修改时间')->display(function($value){
            return format_time($value);
        });
        $grid->warningSetting()->warninger_id('报警通道');

        $grid->disableCreateButton();
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
//            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('sender_id', '主机编码');
            $filter->in('status','状态')->checkbox([
                '1'    => '正常',
                '2'    => '报废',
            ]);
            $filter->in('supplier_model','型号')->checkbox(
                Sender::SUPPLIER_PRODUCT_MODEL
            );
            $filter->where(function ($query) {
                switch ($this->input) {
                    case 'yes':
                        // custom complex query if the 'yes' option is selected
                        $query->has('warningSetting');
                        break;
                    case 'no':
                        $query->doesntHave('warningSetting');
                        break;
                }
            }, '报警通道')->radio([
                '' => '全部',
                'no' => '未设置',
                'yes' => '已设置',
            ]);

        });
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append('<a target="_blank" href="'.route('ccrp.login',$actions->row->company_id).'"><i class="fa fa-laptop"></i></a>');
            if (in_array($actions->row->supplier_model,Sender::LENGWANG_PRODUCT_MODEL)) {
                $actions->append('<a target="_blank" href="' . route('ccrp.sender_instruct', $actions->row->id) . '"><i class="fa fa-cogs" title="下发指令"></i></a>');
            }

        });
        $grid->fixColumns(0, -1);
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id, $param = null)
    {
        if ($id) {
            $show = new Show(Sender::findOrFail($id));
        } else {
            $show = new Show(Sender::where(key($param), $param[key($param)])->where('status', 1)->firstOrFail());
        }

        $show->id('Id');
        $show->sender_id('主机号');

        $show->status('状态')->using([1 => '正常', 0 => '禁用']);
        $show->note('备注');
        $show->note2('备注2');

//        $show->supplier_id('Supplier id');
//        $show->company_id('单位id');
        $show->company('单位名称')->as(function ($value) {
            return $this->company->title;
        });
        $show->company_address('单位地址')->as(function ($value) {
            return $this->company->address;
        });
        $show->company_contact('联系方式')->as(function ($value) {
            return $this->company->manage . ' , ' . $this->company->tel . ' , ' . $this->company->phone;
        });

        $show->company_username('登录账号密码')->as(function ($value) {
            return $this->company->username . ' , ' . $this->company->password;
        });

        $show->category_id('分类id');

        $show->supplier_model('型号');
        $show->simcard('Sim卡号');
        $show->ischarging('市电')->using([1 => '插电', 0 => '断电']);
        $show->ischarging_update_time('市电状态改变时间')->as(function ($value) {
            return date('Y-m-d H:i', $value);
        });
//        $show->install_uid('Install uid');
        $show->install_time('安装时间')->as(function ($value) {
            return date('Y-m-d H:i', $value);
        });
//        $show->uninstall_time('Uninstall time');
//        $show->update_time('Update time');
        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });;
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Sender);

        $form->switch('status', 'Status')->default(1);
        $form->text('note', 'Note');
        $form->text('note2', 'Note2');
        $form->text('simcard', 'Simcard');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
