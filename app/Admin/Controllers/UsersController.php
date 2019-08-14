<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\UpdateRow;
use App\Models\App;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use function app\Utils\arraySort;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use Illuminate\Http\Request;

class UsersController extends AdminController
{
    use HasResourceActions;


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->model()->orderBy('id','desc');
        $grid->id('Id');
        $grid->name('用户名name');
        $grid->roles('角色')->pluck('name')->label();
        $grid->apps('绑定系统')->pluck('name')->label();
        $grid->phone('电话号码')->display(function ($value) {
            return $this->phone . '' . ($this->phone_verified ? '<span class="label label-success">已验证</span>' : '<span class="label label-danger">未验证</span>');
        });
        $grid->realname('真实姓名');
        $grid->column('weuser.headimgurl','微信头像')->display(function ($value) {
            return '<img style="width:50px;" src="' . $value . '">';
        });
        $grid->column('weuser.nickname','昵称');
        $grid->column('weuser.sex','性别')->display(function ($value) {
            return $value ? ($value == 1 ? "男" : "女") : "-";
        });
        $grid->area('地区')->display(function ($value) {
            if($this->weuser)
            {
                return $this->weuser->country . ',' . $this->weuser->province . ',' . $this->weuser->city;
            }else{
                return '-';
            }
        });
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');


        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('name', '昵称');
            $filter->like('realname', '真实姓名');
            $filter->like('phone', '手机号');
            $filter->equal('phone_verified','手机验证')->radio([
                ''   => '所有',
                0    => '未验证',
                1    => '已验证',
            ]);

            $filter->where(function ($query) {
                foreach($this->input as $item)
                {
                    $query->whereHas('roles', function ($query) use ($item) {
                        $query->where('role_id', $item);
                    });
                }
            }, '角色')->checkbox(Role::pluck('name','id'));
            $filter->where(function ($query) {
                foreach($this->input as $item)
                {
                    $query->whereHas('apps', function ($query) use ($item) {
                        $query->where('app_id', $item);
                    });
                }
            }, '绑定系统')->checkbox(App::where('status',1)->pluck('name','id'));
            $filter->where(function ($query) {
                $query->whereHas('apps', function ($query) {
                    $query->where('app_unitid', $this->input);
                });
            }, '绑定系统的单位ID');
        });
        $grid->disableActions();

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch)  {
                $batch->disableDelete();
//                $batch->add('删除【试用】角色', new UpdateRow(route('users.remove_roles'), '1'));
            });
        });

        return $grid;
    }

    public function removeRoles(Request $request)
    {
        $role_id = $request->get('function');
        $roles = RoleHasUser::where('role_id',$role_id)->whereIn('user_id',$request->get('ids'))->get();
        /** @var RoleHasUser $item */
        foreach ($roles as $item) {
            $item->delete();
        }
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->name('昵称');
        $show->phone('手机');
        $show->phone_verified('手机是否验证')->using(['1'=>'是','0'=>'否']);
        $show->realname('真实姓名');
//        $show->email('邮箱');
        $show->created_at('注册日期');

        $show->weuser('微信', function ($show) {

            $show->nickname('昵称');
            $show->headimgurl('头像')->image();
            $show->sex('性别')->using(['2' => '女', '1' => '男']);
            $show->language('语言');
            $show->address('地址')->as(function($value){
                return $this->country . '' .$this->province . '' .$this->city . '' ;
            });
            $show->updated_at('同步日期');

            $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                });;

            $show->weids('微信IDs', function ($grid) {
                $grid->disableCreateButton();
                $grid->disablePagination();
                $grid->disableExport();
                $grid->disableRowSelector();
                $grid->disableFilter();
                $grid->disableActions();
                $grid->weapp()->name('应用');
                $grid->weapp_id();
                $grid->openid();
                $grid->unionid();
            });
        });

        $show->apps('绑定App', function ($grid) {
            $grid->disableCreateButton();
            $grid->disablePagination();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->disableActions();

            $grid->app_id();
            $grid->name('系统名称');
            $grid->app_username('系统用户名');
            $grid->app_userid();
            $grid->app_unitid();
            $grid->created_at('绑定时间');
        });

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
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
        $form = new Form(new User);

        $form->text('name', 'Name');
        $form->mobile('phone', 'Phone');
        $form->switch('phone_verified', 'Phone verified');
        $form->text('realname', 'Realname');
        $form->email('email', 'Email');

        $form->multipleSelect('roles', trans('admin.roles'))->options(Role::all()->pluck('name', 'id'));
        $form->multipleSelect('apps', '绑定管理系统')->options(App::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options(Permission::all()->pluck('name', 'id'));
        return $form;
    }

    public function destroy($id)
    {

        $user = User::find($id);
        $weuser = $user->weuser;
        if($weuser)
        {
            $weids = $weuser->weids;
            foreach($weids as $weid)
            {
                $weid->delete();
            }
            $weuser->delete();
        }
        if ($this->form()->destroy($id)) {
            $data = [
                'status' => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status' => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }


}
