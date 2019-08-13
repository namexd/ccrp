<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Area;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Tree;

class AreasController extends Controller
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
            ->header('用户菜单')
            ->description('列表')
            ->row(function (Row $row) {
                $row->column(8, $this->treeView()->render());
            });
    }

    public function all(Content $content)
    {
        return $content
            ->header('所有地区')
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
        $grid = new Grid(new Area);

        $grid->id('Id');
        $grid->name('Name');
        $grid->parent_id('Parent id');
        $grid->short_name('Short name');
        $grid->level_type('Level type');
        $grid->city_code('City code');
        $grid->zip_code('Zip code');
        $grid->merger_name('Merger name');
        $grid->lng('Lng');
        $grid->lat('Lat');
        $grid->pinyin('Pinyin');
        $grid->status('Status');
        $grid->count_company('Count company');
        $grid->count_company_ll('Count company ll');
        $grid->count_company_ll2('Count company ll2');
        $grid->count_company_swzp('Count company swzp');
        $grid->count_warning('Count warning');

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
        $show = new Show(Area::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->parent_id('Parent id');
        $show->short_name('Short name');
        $show->level_type('Level type');
        $show->city_code('City code');
        $show->zip_code('Zip code');
        $show->merger_name('Merger name');
        $show->lng('Lng');
        $show->lat('Lat');
        $show->pinyin('Pinyin');
        $show->status('Status');
        $show->count_company('Count company');
        $show->count_company_ll('Count company ll');
        $show->count_company_ll2('Count company ll2');
        $show->count_company_swzp('Count company swzp');
        $show->count_warning('Count warning');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Area);

        $form->text('name', 'Name');
        $form->number('parent_id', 'Parent id');
        $form->text('short_name', 'Short name');
        $form->text('level_type', 'Level type');
        $form->text('city_code', 'City code');
        $form->text('zip_code', 'Zip code');
        $form->text('merger_name', 'Merger name');
        $form->text('lng', 'Lng');
        $form->text('lat', 'Lat');
        $form->text('pinyin', 'Pinyin');
        $form->switch('status', 'Status')->default(1);
        $form->number('count_company', 'Count company');
        $form->number('count_company_ll', 'Count company ll');
        $form->number('count_company_ll2', 'Count company ll2');
        $form->number('count_company_swzp', 'Count company swzp');
        $form->number('count_warning', 'Count warning');

        return $form;
    }


    /**
     * @return \Encore\Admin\Tree
     */
    protected function treeView()
    {
        $menuModel = Area::class;

        $treemodel = $menuModel::tree(function ($tree) {
            $tree->query(function ($model) {
                return $model->where('id', 1);
            });
        });
        $treemodel->disableCreate();
        $treemodel->disableSave();
        $treemodel->disableRefresh();
        $treemodel->branch(function ($branch) {

            $payload = '';
            $payload .= "&nbsp;<strong>{$branch['id']} : {$branch['name']}</strong>";
            if ($branch['admin_company'] ) {
                foreach ($branch['admin_company'] as $item) {
                    $payload .= '<span class="label label-primary">' . $item['title'] . '</span> ';
                }
            }else{
                $payload .= '<a class="btn btn-danger btn-xs" target="_blank" href="'.route('ccrp.companies.create_cdc_admin_by_area',['area'=>$branch['id']]).'">创建：' . $branch['name'] . ' </a> ';
            }
            return $payload;
        });
        $treemodel->query(function ($model) {
            dd($model);
            return $model->where('id', 1);
        });

        $treemodel->setView([
            'tree' => 'admin/tree/tree_view',
            'branch' => 'admin/tree/tree_view_branch',
        ]);
        return $treemodel;
    }
}
