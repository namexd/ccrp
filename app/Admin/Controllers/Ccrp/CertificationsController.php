<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Certification;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\File;
use App\Models\Upload;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;
use Illuminate\Http\Request;

class CertificationsController extends Controller
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
            ->header('第三方校准证书')
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
        $grid = new Grid(new Certification);

        $grid->model()->orderBy('id', 'desc');
        $grid->id('Id');
        $grid->customer('委托单位');
        $grid->instrument_no('产品编号');
        $grid->instrument_name('产品名称');
        $grid->instrument_model('产品型号');
        $grid->certificate_year('年份');
        $grid->out_date('有效期');
        $grid->file('第一页')->display(function ($value) {
            return '<a href="' . ($value['file_server'] . $value['file_url']) . '" target="_blank">' . $value['id'] . '</a>';
        });
        $grid->files('其他页')->display(function ($item) {
            $files = $this->files();
            $url = '';
            foreach ($files as $value) {
                $url .= '<a href="' . ($value['file_server'] . $value['file_url']) . '" target="_blank">' . $value['id'] . '</a>';
            }
            return $url;
        });
        $grid->company()->title('绑定单位');
        $grid->payCompany()->title('付费单位');

        $grid->filter(function ($filter) {
            $filter->like('company_id', '单位id');
            $filter->like('customer', '委托单位');
            $filter->like('instrument_no', '产品编码');
        });
        $grid->tools(function ($tools) {
            $tools->append('<a class="btn btn-sm btn-danger form-history-bac" style="float: right;margin-right: 20px;" href="certifications/createBatch" ><i class="fa fa-plus-circle"></i>批量添加</a>');
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
        $show = new Show(Certification::findOrFail($id));

        $show->id('Id');
        $show->certificate_no('Certificate no');
        $show->certificate_year('Certificate year');
        $show->out_date('Out date');
        $show->customer('Customer');
        $show->customer_address('Customer address');
        $show->instrument_name('Instrument name');
        $show->manufacturer('Manufacturer');
        $show->model('Model');
        $show->instrument_no('Instrument no');
        $show->instrument_accuracy('Instrument accuracy');
        $show->file_id('File id');
        $show->file_ids('File ids');
        $show->pay_company_id('Pay company id');
        $show->company_id('Company id');
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
        $form = new Form(new Certification);

        $form->select('company_id', '绑定单位')->options(function ($id) {
            $company = Company::find($id);

            if ($company) {
                return [$company->id => $company->title];
            }
        })->ajax('/admin/api/companies');
        $form->text('customer', '委托单位')->default('东莞市疾病预防控制中心');
        $form->text('instrument_no', '产品编号');

        $form->number('certificate_year', '检测年份')->default(Carbon::now()->year - 1);
        $form->text('certificate_no', '证书编号');
        $form->datetime('out_date', '有效期（建议复校日期）')->default(date('Y-m-d'));
        $form->text('customer_address', '委托单位地址');
        $form->text('instrument_name', '产品名称')->default('无线温湿度探头');
        $form->text('manufacturer', '生产商')->default('上海冷王智能科技有限公司');
        $form->text('instrument_model', '型号')->default('LWTG310');
        $form->text('instrument_accuracy', '校准结果');
        $form->text('file_id', '上传首页');
        $form->text('file_ids', '上传其他页');
//        $form->file('file_id', '上传首页');
//        $form->multipleFile('file_ids', '上传其他页');
        $form->select('pay_company_id', '付费单位')->options(function ($id) {
            $company = Company::find($id);

            if ($company) {
                return [$company->id => $company->title];
            }
        })->ajax('/admin/api/companies');
        $form->hidden('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->hidden('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $form;
    }

    public function createBatch(Content $content)
    {


        $form = new Form(new Certification);
        $form->select('company_id', '绑定单位')->options(function ($id) {
            $company = Company::find($id);

            if ($company) {
                return [$company->id => $company->title];
            }
        })->ajax('/admin/api/companies');
        $form->text('customer', '委托单位')->default('东莞市疾病预防控制中心');
        $form->text('instrument_no', '产品编号')->readOnly()->placeholder('直接读取文件名');
        $form->number('certificate_year', '检测年份')->default(Carbon::now()->year - 1);
        $form->text('certificate_no', '证书编号');
        $form->datetime('out_date', '有效期（建议复校日期）')->default('');
        $form->text('customer_address', '委托单位地址');
        $form->text('instrument_name', '产品名称')->default('无线温湿度探头');
        $form->text('manufacturer', '生产商')->default('上海冷王智能科技有限公司');
        $form->text('instrument_model', '型号')->default('LWTG310');
        $form->text('instrument_accuracy', '校准结果');
        $form->textarea('files', '探头图片清单')->placeholder('探头号.jpg，每行一个');
//        $form->file('file_id', '上传首页');
//        $form->multipleFile('file_ids', '上传其他页');
        $form->select('pay_company_id', '付费单位')->options(function ($id) {
            $company = Company::find($id);

            if ($company) {
                return [$company->id => $company->title];
            }
        })->ajax('/admin/api/companies');
        $form->hidden('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->hidden('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        $form->setAction(route('ccrp.certifications.saveBatch'));

        return $content
            ->header('批量上传')
            ->description('CreateBatch')
            ->body($form);
    }

    private function getSensorSn($file_name)
    {
        return explode('-',$file_name)[0];
    }
    private function getFilename($file_name)
    {
        return explode('.',$file_name)[0];
    }
    public function saveBatch(Request $request)
    {

        $data = $request->all();
        $files = $request->get("files");
        $files_arr = explode("\r\n",$files);
        $certification = $request->all();
        unset($certification['files']);
        foreach($files_arr as $file_name)
        {
            $file = File::where('file_name',$this->getFilename($file_name))->where('note',$data['certificate_year'])->first();
            if(!$file)
            {
                $file = new File();
                $file->file_name = $this->getFilename($file_name);
                $file->file_server  = 'https://oss.coldyun.net/';
                $file->file_url  = '/www/web/we_coldyun_net/ccrp/certifications/2018/dgcdc/'.$file_name;
                $file->file_type  = '.jpg';
                $file->file_category  = '第三方校准证书';
                $file->file_desc  = '';
                $file->company_id  =$data['company_id'];
                $file->company_name  =$data['customer'];
                $file->create_time  =time();
                $file->out_date  = '';
                $file->file_url2  = '';
                $file->status  = 1;
                $file->note  = $data['certificate_year'];
                $file->save();
            }
        }

        $sensors =[];
        foreach($files_arr as $file_name)
        {
            $sensor = $this->getSensorSn($file_name);
            if(!in_array($sensor,$sensors))
            {
                $sensors[] = $this->getSensorSn($file_name);
            }
        }

        unset($sensor);
        foreach($sensors as $sensor)
        {
            $certificat = Certification::where('instrument_no',$sensor)->where('certificate_year',$data['certificate_year'])->first();
            if(!$certificat)
            {
                $certificat = new Certification();
                $certificat->certificate_year = $data['certificate_year'];
                $certificat->customer = $data['customer'];
                $certificat->certificate_year = $data['certificate_year'];
                $certificat->certificate_no = $data['certificate_no'];
                $certificat->out_date = $data['out_date'];
                $certificat->customer_address = $data['customer_address'];
                $certificat->instrument_no = $sensor;
                $certificat->instrument_name =$data['instrument_name'];
                $certificat->manufacturer = $data['manufacturer'];
                $certificat->instrument_model = $data['instrument_model'];
                $certificat->instrument_accuracy = $data['instrument_accuracy'];
                $certificat->company_id = $data['company_id'];
                $certificat->pay_company_id = $data['pay_company_id'];
                $files = File::where('file_name','like',$sensor.'-%')->select('id','file_name')->orderBy('file_name','asc')->get();
                $certificat->file_id = $files[0]->id??null;
                $certificat->file_ids = $files[1]->id??null;
                $certificat->created_at = Carbon::now()->toDateTimeString();;
                $certificat->updated_at = Carbon::now()->toDateTimeString();;
                $certificat->save();

            }
        }

        return admin_toastr('插入成功');
    }
}
