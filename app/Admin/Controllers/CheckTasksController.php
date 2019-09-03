<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Check\CheckTaskShow;
use App\Admin\Actions\Check\ExportPDF;
use App\Admin\Actions\Check\ExportWord;
use App\Admin\Extensions\Tools\BuildTask;
use App\Libs\tools\word;
use App\Models\App;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\CheckTask;
use App\Http\Controllers\Controller;
use App\Models\Ccrp\CheckTaskResult;
use App\Models\Ccrp\CheckTemplate;
use function App\Utils\microservice_access_encode;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;

class CheckTasksController extends Controller
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
            ->header('巡检任务')
            ->description('定时任务')
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
        $result = CheckTaskResult::where('task_id', $id);
        $params = $result->pluck('value', 'key')->toArray();
        $params['result'] = $result->first();
        $view = view('admin.ccrp.template.check_report', $params);
        return $view;
//        return $content
//            ->header('巡检清单')
//            ->row(function (Row $row) use($view) {
//                $row->column(12, function (Column $column) use($view) {
//                    $column->append((new Box(trans('admin.detail'), $view)));
//                });
//            });
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
        $grid = new Grid(new CheckTask);
        $grid->disableCreateButton();
        $grid->id('Id');
        $grid->company()->title('单位');
        $grid->template()->title('模板');
        $grid->start('开始时间')->display(function ($value) {
            return date('Y-m-d', $value);
        });
        $grid->end('结束时间')->display(function ($value) {
            return date('Y-m-d', $value);
        });
        $grid->status('Status');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->tools(function ($tools) {
//            $tools->append(new BuildTask());
        });
        $grid->actions(function ($action) {
            $action->disableDelete();
            $action->disableEdit();
            $action->disableView();
            $action->add(new CheckTaskShow());
            $action->add(new ExportWord());
            $action->add(new ExportPDF());
        });

        
        $grid->filter(function ($filter) {
            $filter->where(function ($query) {
                $company = Company::find($this->input);
                $company_ids = $company->ids();
                $query->whereIn('company_id', $company_ids);
            }, '单位名称')->select()->ajax('/admin/api/companies');

            $filter->where(function ($query){
                   $query->where('start','<=',strtotime($this->input))->where(function ($query){
                       $query->where('end','>=',strtotime($this->input));
               });
            },'日期选择')->date();
        });
        return $grid;
    }

    public function export_word($id)
    {
        $result = CheckTaskResult::where('task_id', $id);
        $params = $result->pluck('value', 'key')->toArray();
        $params['result'] = $result->first();
        $content = view('admin.ccrp.template.check_report', $params);
        $word = new word();
        $word->start();
        //$html = "aaa".$i;
        $wordname = $result->first()->task->company->title."冷链监测系统".CheckTemplate::CYCLE_TYPE[$result->first()->task->template->cycle_type]."巡检报表.doc";
        echo $content;
        $word->save($wordname);
        ob_flush();//每次执行前刷新缓存
        flush();
        die();

    }

    public function export_pdf($id)
    {
        $result = CheckTaskResult::where('task_id', $id);
        $params = $result->pluck('value', 'key')->toArray();
        $params['result'] = $result->first();
        $content = view('admin.ccrp.template.check_report', $params);
        $app = App::where('slug', 'ccsc.admin')->first();
        $file_name = $result->first()->task->company->title."冷链监测系统".CheckTemplate::CYCLE_TYPE[$result->first()->task->template->cycle_type]."巡检报表";
        $url = 'https://export-ms.coldyun.net/api/';
        $access = microservice_access_encode($app->appkey, $app->appsecret, ['test' => 'test']);
        $client = new Client();
        $res = $client->request('POST', $url.'store', [
            'headers' => [
                'access' => $access,
            ],
            'form_params' => [
                'data' => $content->render(),
                'file_type' => 'pdf',
                'data_type' => 'html',
                'file_name' => $file_name
            ]
        ]);
        $resp = json_decode($res->getBody()->getContents(), true);
        $url = $resp['url'];
        return redirect($url);
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CheckTask::findOrFail($id));

        $show->id('Id');
        $show->company_id('Company id');
        $show->template_id('Template id');
        $show->status('Status');
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
        $form = new Form(new CheckTask);

        $form->number('company_id', 'Company id');
        $form->number('template_id', 'Template id');
        $form->switch('status', 'Status');

        return $form;
    }

    public function buildTask()
    {
        if ((new CheckTask())->buildTask()) {
            return ['code' => 1];
        } else {
            return ['code' => 0];
        }
    }
}
