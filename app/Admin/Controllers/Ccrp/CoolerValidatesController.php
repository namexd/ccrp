<?php

namespace App\Admin\Controllers\Ccrp;

use App\Admin\Extensions\Tools\UpdateField;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerDetail;
use App\Models\Ccrp\CoolerValidate;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class CoolerValidatesController extends Controller
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
            ->header('冰箱资料调查表')
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
        $dgcdc = Company::where('id',26)->first();
        $no_validate = Cooler::where('status','!=',4)->whereIn('company_id',$dgcdc->ids())->doesntHave('validate')->get();
        foreach ($no_validate as $item) {
            $new = $item->validate()->create([
                'company_id' => $item->company_id,
                'company_title' => $item->company->title,
                'cooler_id' => $item->cooler_id,
                'cooler_sn' => $item->cooler_sn,
                'cooler_name' => $item->cooler_name,
            ]);
        }
        admin_toastr('新增'.count($no_validate).'台');
        return back();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CoolerValidate);
        $grid->model()->orderBy('id','desc');
        $grid->id('Id');
        $grid->company_id('Company id');
        $grid->company_title('单位名称');
        $grid->cooler_id('Cooler id');
        $grid->cooler_sn('设备编号');
        $grid->cooler_name('C设备名称');
//        $grid->cooler_cdc_sn('NIPIS国家免疫规划系统编码');
        $grid->cooler_type('设备类型');//->select(CoolerValidate::COOLER_TYPE);
        $grid->comporation('生产企业');
        $grid->cooler_brand('品牌');
        $grid->cooler_model('型号');
        $grid->is_medical('医用');
        $grid->medical_permission('许可证');
        $grid->cooler()->is_medical('医用（冰箱表）')->display(function ($value){
            return Cooler::IS_MEDICAL[$value];
        });
        $grid->cooler()->status('冰箱状态')->display(function ($value){
            return Cooler::STATUSES[$value];
        });
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->validate_status('填报状态')->using(['0'=>'否','1'=>'是']);
        $grid->update_to_cooler('同步状态')->using(['0'=>'否','1'=>'是']);

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('company_id', '单位ID');
            $filter->equal('cooler_sn', '冰箱编码');
            $filter->equal('cooler_cdc_sn', 'NIPIS国家免疫规划系统编码');
            $filter->like('cooler_name', '设备名称');
            $filter->like('cooler_model', '产品型号');
            $filter->equal('cooler_type', '冰箱类型')->radio(CoolerValidate::COOLER_TYPE);
            $filter->equal('is_medical', '医用')->radio(CoolerValidate::IS_MEDICAL);
            $filter->where(function ($query) {
                $value = $this->input;
                $query->whereHas('cooler', function ($query) use ($value) {
                    $query->where('is_medical', '=',$value);
                })->get();
            }, '医用（冰箱表）', 'is_medical_check')->radio([
                '' => '所有',
                '1' => '否',
                '2' => '是',
            ]);
            $filter->equal('validate_status', '填报状态')->radio(['0'=>'否','1'=>'是']);
            $filter->equal('update_to_cooler', '同步状态')->radio(['0'=>'否','1'=>'是']);
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->append('<br><a target="_blank" href="' . route('coolers.edit', $actions->row->cooler_id) . '"> <i class="fa fa-book"></i></a>');
            $actions->append('<br><a target="_blank" href="' . route('ccrp.login', $actions->row->company_id) . '"> <i class="fa fa-laptop"></i></a>');
        });


        $grid->tools(function ($tools) {
            $tools->batch(function ($batch)  {
                $batch->disableDelete();
                $batch->add('同步到cooler', new UpdateField(route('ccrp.cooler_validate.update_to_cooler'), 'time', time()));
            });
        });

        return $grid;
    }

    protected function updateToCooler(Request $request)
    {
//        $field = $request->get('field');
//        $value = $request->get('value');
        foreach (CoolerValidate::find($request->get('ids')) as $item) {
            $cooler_id = $item->cooler_id;
            $cooler = Cooler::find($cooler_id);
//            dd($item->toArray());
            $coolerDetails = new CoolerDetail();
            $data = $item->toArray();

            $extra['nipis_code'] = $data['cooler_cdc_sn'];
            $extra['cooler_type'] = $data['cooler_type'];
            $extra['come_from'] = $data['come_from'];
            $extra['comporation'] = $data['comporation'];
            $extra['model'] = $data['cooler_model'];
            $extra['product_sn'] = $data['product_sn'];
            $extra['product_date'] = $data['product_date'];
            $extra['arrive_date'] = $data['arrive_date'];
            $extra['use_date'] = $data['cooler_starttime'];
            $extra['is_medical'] = $data['is_medical'];
            $extra['medical_permission'] = $data['medical_permission'];
            $extra['has_double_power'] = $data['has_double_power'];
            $extra['has_power_generator'] = $data['has_power_generator'];
            $extra['has_double_compressor'] = $data['has_double_compressor'];
            $extra['cooler_status'] = $data['cooler_status'];
            $extra['validate_name'] = $data['validate_name'];
            $extra['validate_status'] = $data['validate_status'];
            $cooler->saveDetails($extra);
//            $cooler = Cooler::find($cooler_id);
//            if($item->cooler_cdc_sn)
//            {
//                $cooler->nipis_code = $item->cooler_cdc_sn;
//            }
//            $cooler->is_medical = $item->is_medical=='是'?2:1;
//            $cooler->come_from = $item->come_from;
//            $cooler->comporation = $item->comporation;
//            $cooler->cooler_model = $item->cooler_model;
//            $cooler->cooler_starttime = $item->cooler_starttime;
//            $cooler->cooler_size = $item->cooler_size;
//            $cooler->cooler_size2 = $item->cooler_size2;
//            $cooler->save();
            $item->update_to_cooler = 1;
            $item->save();

//           dd($item->toArray());
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
        $show = new Show(CoolerValidate::findOrFail($id));

        $show->id('Id');
        $show->company_id('Company id');
        $show->company_title('Company title');
        $show->cooler_id('Cooler id');
        $show->cooler_sn('Cooler sn');
        $show->cooler_name('Cooler name');
        $show->cooler_cdc_sn('Cooler cdc sn');
        $show->cooler_type('Cooler type');
        $show->come_from('Come from');
        $show->comporation('Comporation');
        $show->cooler_brand('Cooler brand');
        $show->cooler_model('Cooler model');
        $show->product_sn('Product sn');
        $show->product_date('Product date');
        $show->arrive_date('Arrive date');
        $show->cooler_starttime('Cooler starttime');
        $show->is_medical('Is medical');
        $show->medical_permission('Medical permission');
        $show->has_double_power('Has double power');
        $show->has_power_generator('Has power generator');
        $show->has_double_compressor('Has double compressor');
        $show->cooler_status('Cooler status');
        $show->validate_name('Validate name');
        $show->validate_status('Validate status');
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
        $form = new Form(new CoolerValidate);

        $form->text('cooler_name', '设备名称')->readOnly();
        $form->text('cooler_sn', '设备编号')->readOnly();
        $form->text('cooler_cdc_sn', '国家免疫规划信息系统编码');
        $form->radio('cooler_type', '设备类型')->options([
            '普通冷库' => '普通冷库',
            '低温冷库' => '低温冷库',
            '普通冰箱' => '普通冰箱',
            '低温冰箱' => '低温冰箱',
            '台式小冰箱  ' => '台式小冰箱  ',
        ]);
        $form->radio('come_from', '设备来源')->options([
            '本级自购' => '本级自购',
            '上级下发' => '上级下发',
            '设备迁入' => '设备迁入',
            '捐赠' => '捐赠',

        ]);
        $form->text('comporation', '生产企业');
        $form->text('cooler_brand', '品牌');
        $form->text('cooler_model', '设备型号');
        $form->text('product_sn', '出厂编号');
        $form->date('product_date', '出厂日期')->default(date('Y-m-d '));
        $form->date('arrive_date', '到货日期')->default(date('Y-m-d'));
        $form->date('cooler_starttime', '启用日期')->default(date('Y-m-d'));
        $form->switch('is_medical', '是否具备医疗器械注册证的医用冰箱')->states([
            'on' => ['value' => '是'],
            'off' => ['value' => '否'],
        ]);
        $form->text('medical_permission', '医疗器械注册证编号');
        $form->switch('has_double_power', '是否采用双路供电')->states([
            'on' => ['value' => '是'],
            'off' => ['value' => '否'],
        ]);
        $form->switch('has_power_generator', '是否有全自动发电机组')->states([
            'on' => ['value' => '是'],
            'off' => ['value' => '否'],
        ]);
        $form->switch('has_double_compressor', '是否配备双压缩机(冷库)')->states([
            'on' => ['value' => '是'],
            'off' => ['value' => '否'],
        ]);
        $form->radio('cooler_status', '设备状态')->options([
            '正常' => '正常',
            '待修' => '待修',
            '报废' => '报废',
            '备用' => '备用',
            '迁出' => '迁出',
        ]);
        $form->text('validate_name', '填报人姓名');
        $form->switch('validate_status', '填报状态');
        $form->hidden('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->hidden('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
