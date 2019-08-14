<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\App;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Reports\StatMange;
use App\Http\Controllers\Controller;

use function app\Utils\microservice_access_encode;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Request;

class StatManageController extends Controller
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
            ->header('冷链管理评估表下载')
            ->description(request()->get('start').'--'.request()->get('end'))
            ->row(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->append((new Box(' ', $this->searchForm()->render())));
                });
            });
    }

    public function searchForm()
    {
        $form = new \Encore\Admin\Widgets\Form();
        $form->method('GET');
        $form->action('/admin/ccrp/stat_manages_export');
        $form->select('company_id', '单位')->ajax('/admin/api/companies');
        $form->date('start', '开始日期')->placeholder('选择开始日期')->default(Carbon::now()->subMonth(3)->firstOfMonth()->toDateString());
        $form->date('end', '结束日期')->placeholder('选择结束日期')->default(Carbon::now()->subMonth(1)->toDateString());
        $form->disableReset();
        return $form;
    }

    public function dataTable()
    {
        $company_id = Request::get('company_id');
        $company_ids = Company::find($company_id)->ids();
        $start_time = Request::get('start');
        $end_etime = Request::get('end');
        $headers = ['单位', '分类', '冷链设备总数', '报警总数', '人为造成次数', '未及时处理次数', '未按规定登录平台次数', '冷链管理评估值'];
        $rows = (new StatMange())->getListByMonths($company_ids, $start_time, $end_etime);
        $tables = [];
        foreach ($rows as $row) {
            $tables[] = [
                $row->company->title,
                Company::COMPANY_TYPE[$row->company->company_type],
                $row->devicenum,
                $row->totalwarnings,
                $row->humanwarnings,
                $row->highlevels,
                $row->unlogintimes,
                $row->grade,
            ];
        }
        $table = new Table($headers, $tables);

        return $table;
    }


    public function export()
    {
        $company_id = Request::get('company_id');
        $company = Company::find($company_id);
        $company_ids = $company->ids();
        $start_time = Request::get('start');
        $end_etime = Request::get('end');
        $rows = (new StatMange())->getListByMonths($company_ids, $start_time, $end_etime);
        $list=[];
        $k=0;
        $devicenum=0;$totalwarnings=0;$humanwarnings=0;$highlevels=0;$unlogintimes=0;
        foreach ($rows as $key => $value) {
            $k++;
            $list[] = [
                $value->company->title,
                Company::COMPANY_TYPE[$value->company->company_type],
                $value->devicenum,
                $value->totalwarnings,
                $value->humanwarnings,
                $value->highlevels,
                $value->unlogintimes,
                $value->grade,
            ];
            $devicenum=+ $value->devicenum;
            $totalwarnings=+ $value->totalwarnings;
            $humanwarnings=+ $value->humanwarnings;
            $highlevels=+ $value->highlevels;
            $unlogintimes=+ $value->unlogintimes;

        }
        $list[$k]=['','合计',$devicenum,$totalwarnings,$humanwarnings,$highlevels,$unlogintimes];
        $headers = ['单位', '分类', '冷链设备总数', '报警总数', '人为造成次数', '未及时处理次数', '未按规定登录平台次数', '冷链管理评估值'];
        $app = App::where('slug', 'ccsc.admin')->first();
        $file_name = $company->title.'('.$start_time.'--'.$end_etime.')冷链管理评估表';
        $url = 'https://export-ms.coldyun.net/api/';
        $access = microservice_access_encode($app->appkey, $app->appsecret, ['test' => 'test']);
        $data = [['title' => $file_name, 'data' => $list]];
        $client = new Client();
        $res = $client->request('POST', $url.'store', [
            'headers' => [
                'access' => $access,
            ],
            'form_params' => [
                'table_headers' => $headers,
                'data' => json_encode($data),
                'file_name' => $file_name
            ]
        ]);
//        dd($res->getBody()->getContents()) ;
        $resp = json_decode($res->getBody()->getContents(), true);
        $url = $resp['url'];
        return redirect($url);
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
        $grid = new Grid(new StatMange);
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->company()->title('单位');
        $grid->type('分类')->display(function () {
            return Company::COMPANY_TYPE[$this->company['company_type']];
        });
        $grid->devicenum('冷链设备总数');
        $grid->totalwarnings('报警总数');
        $grid->humanwarnings('人为造成次数');
        $grid->highlevels('未及时处理次数');
        $grid->unlogintimes('未按规定登录平台次数');
        $grid->grade('冷链管理评估值');
        $grid->filter(function ($filter) {
            $filter->where(function ($query) {
                $company = Company::find($this->input);
                $company_ids = $company->ids();
                $query->whereIn('company_id', $company_ids);
            }, '单位名称')->select()->ajax('/admin/api/companies');
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
        $show = new Show(StatMange::findOrFail($id));

        $show->id('Id');
        $show->company_id('Company id');
        $show->year('Year');
        $show->month('Month');
        $show->devicenum('Devicenum');
        $show->totalwarnings('Totalwarnings');
        $show->humanwarnings('Humanwarnings');
        $show->highlevels('Highlevels');
        $show->unlogintimes('Unlogintimes');
        $show->grade('Grade');
        $show->create_time('Create time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StatMange);

        $form->number('company_id', 'Company id');
        $form->number('year', 'Year');
        $form->number('month', 'Month');
        $form->number('devicenum', 'Devicenum');
        $form->number('totalwarnings', 'Totalwarnings');
        $form->number('humanwarnings', 'Humanwarnings');
        $form->number('highlevels', 'Highlevels');
        $form->number('unlogintimes', 'Unlogintimes');
        $form->decimal('grade', 'Grade');
        $form->number('create_time', 'Create time');

        return $form;
    }
}
