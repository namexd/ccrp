<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\App;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Reports\StatCooler;
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

class StatCoolerController extends Controller
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
            ->header('冷链设备评估表')
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
        $form->action('/admin/ccrp/stat_coolers_export');
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
        $headers = ['单位', '分类', '冷链设备总数', '报警总数', '人为造成次数', '未及时处理次数', '未按规定登录平台次数', '冷链设备评估值'];
        $rows = (new StatCooler())->getListByMonths($company_ids, $start_time, $end_etime);
        $tables = [];
        foreach ($rows as $row) {
            $tables[] = [
                $row->cooler->company->title,
                $row->cooler->cooler_name,
                Cooler::COOLER_TYPE[$row->cooler->cooler_type],
                $row->cooler->cooler_brand,
                $row->cooler->cooler_model,
                $row->is_medical ? '是' : '否',
                $row->temp_avg,
                $row->temp_high,
                $row->temp_low,
                $row->temp_variance,
                $row->warning_times,
                $row->error_times,
            ];
        }
        $table = new Table($headers, $tables);

        return $table;
    }


    public function export()
    {
        if (!$company_id = Request::get('company_id')) {
            admin_toastr('请选择单位', 'error');
            return redirect()->back();
        }
        $company = Company::find($company_id);

        $company_ids = $company->ids();

        if (!$cooler_id = Request::get('cooler_id')) {
            $cooler_id = Cooler::whereIn('company_id', $company_ids)->pluck('cooler_id');
        }
        $start_time = Request::get('start');
        $end_etime = Request::get('end');
        $rows = (new StatCooler())->getListByMonths($cooler_id, $start_time, $end_etime);
        foreach ($rows as $key => $row) {
            if ($row->temp_variance == 0)
                continue;
            $temp_type = $row->cooler->collectors->first();
            $wdx_score = $this->wdx_score($row->temp_variance);
            $avg_score = $this->avg_score($temp_type->temp_type, $row->temp_avg);
            $temp_high_score = $this->temp_high_score($temp_type->temp_type, $row->temp_high);
            $temp_low_score = $this->temp_low_score($temp_type->temp_type, $row->temp_low);
            $list[] = [
                'company' => $row->cooler->company->title,
                'cooler_name' => $row->cooler->cooler_name,
                'cooler_type' => Cooler::COOLER_TYPE[$row->cooler->cooler_type],
                'cooler_brand' => $row->cooler->cooler_brand,
                'cooler_model' => $row->cooler->cooler_model,
                'temp_type' => $temp_type->temp_type == 1 ? '冷藏' : '冷冻',
                'is_medical' => $row->is_medical ? '是' : '否',
                'temp_avg' => $row->temp_avg,
                'temp_high' => $row->temp_high,
                'temp_low' => $row->temp_low,
                'temp_variance' => $row->temp_variance,
                'wdx_score' => $wdx_score,
                'avg_score' => $avg_score,
                'temp_high_score' => $temp_high_score,
                'temp_low_score' => $temp_low_score,
                'total_score' => $wdx_score + $avg_score + $temp_high_score + $temp_low_score,
            ];
        }
        $app = App::where('slug', 'ccsc.admin')->first();
        $file_name = $company->title.'('.$start_time.'--'.$end_etime.')冷链设备评估表';
        $url = 'https://export-ms.coldyun.net/api/';
        $access = microservice_access_encode($app->appkey, $app->appsecret, ['note' => '冷链设备评估表']);
        $data = [['title' => $file_name, 'data' => $list]];
        $client = new Client();
        $res = $client->request('POST', $url.'store', [
            'headers' => [
                'access' => $access,
            ],
            'form_params' => [
                'data' => json_encode($data),
                'file_name' => $file_name,
                'template' => 'stat_cooler'
            ]
        ]);
//        dd($res->getBody()->getContents()) ;
        $resp = json_decode($res->getBody()->getContents(), true);
        $url = $resp['url'];
        return redirect($url);
    }

    public function wdx_score($score)
    {
        if ($score <= 0.5 && $score > 0) {
            return 2;
        }
        if ($score <= 1 && $score > 0.5) {
            return 1;
        }
        if ($score <= 2 && $score > 1) {
            return 0;
        }
        if ($score > 2) {
            return -1;
        }
    }

    public function avg_score($temp_type, $score)
    {
        switch ($temp_type) {
            case '1':
                if ($score >= 2 && $score < 8)
                    return 2;
                else return 0;
                break;
            case '2':
                if ($score < -20)
                    return 2;
                if ($score >= -20 && $score < -15)
                    return 1;
                if ($score > -15)
                    return 0;
                break;
            default:
                return 0;
                break;

        }
    }

    public function temp_high_score($temp_type, $score)
    {
        switch ($temp_type) {
            case '1':
                if ($score > 16)
                    return 0;
                if ($score >= 12 && $score < 16)
                    return 1;
                if ($score >= 8 && $score < 12)
                    return 1.5;
                if ($score >= 2 && $score < 8)
                    return 1;
                if ($score < 2)
                    return -10;
                break;
            case '2':
                if ($score < -20)
                    return 2;
                if ($score >= -20 && $score < -15)
                    return 1;
                if ($score >= -15 && $score < -5)
                    return 0;
                if ($score > -5)
                    return -10;
                break;
            default:
                return 0;
                break;

        }
    }

    public function temp_low_score($temp_type, $score)
    {
        switch ($temp_type) {
            case '1':
                if ($score < 0)
                    return -1;
                if ($score >= 0 && $score < 1)
                    return 0;
                if ($score >= 1 && $score < 2)
                    return 1;
                if ($score >= 2 && $score < 8)
                    return 2;
                if ($score > 8)
                    return -10;
                break;
            case '2':
                if ($score < -20)
                    return 2;
                if ($score >= -20 && $score < -15)
                    return 1;
                if ($score >= -15 && $score < -5)
                    return 0;
                if ($score > -5)
                    return -10;
                break;
            default:
                return 0;
                break;

        }
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
        $grid = new Grid(new StatCooler);
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
        $grid->grade('冷链设备评估值');
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
        $show = new Show(StatCooler::findOrFail($id));

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
        $form = new Form(new StatCooler);

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
