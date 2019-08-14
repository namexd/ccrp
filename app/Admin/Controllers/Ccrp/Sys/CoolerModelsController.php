<?php

namespace App\Admin\Controllers\Ccrp\Sys;

use App\Http\Controllers\Controller;
use App\Models\Ccrp\Sys\CoolerBrand;
use App\Models\Ccrp\Sys\CoolerModel;
use App\Models\Ccrp\Sys\CoolerType;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class CoolerModelsController extends Controller
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
        $grid = new Grid(new CoolerModel);

        $grid->id('Id');
        $grid->brand()->name('品牌');
        $grid->name('型号');
        $grid->type()->name('类型');
        $grid->description('描述');
        $grid->cool_volume('冷藏容积（L）');
        $grid->cold_volume('冷冻容积（L）');
        $grid->whole_volume('整体容积（L）');
        $grid->is_medical('是否医用')->switch();
        $grid->created_at('新增时间');
        $grid->updated_at('修改时间');

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('name', '型号');
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
        $show = new Show(CoolerModel::findOrFail($id));

        $show->id('Id');
        $show->brand_id('Brand id');
        $show->type_id('Type id');
        $show->name('Name');
        $show->description('Description');
        $show->cool_volume('Cool volume');
        $show->cold_volume('Cold volume');
        $show->whole_volume('Whole volume');
        $show->is_medical('Is medical');
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
        $form = new Form(new CoolerModel);

        $form->select('brand_id', '品牌')->options(CoolerBrand::all()->pluck('name', 'id'));
        $form->select('type_id', '类型')->options(CoolerType::all()->pluck('name', 'id'));
        $form->text('name', '型号');
        $form->text('description', '描述');
        $form->number('cool_volume', '冷藏容积（L）')->default(0);
        $form->number('cold_volume', '冷冻容积（L）')->default(0);
        $form->number('whole_volume', '整体容积（L）')->default(0);
        $form->switch('is_medical', '是否医用');

        return $form;
    }
}
