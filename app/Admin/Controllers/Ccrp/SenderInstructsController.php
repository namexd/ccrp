<?php

namespace App\Admin\Controllers\Ccrp;

use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Sender;
use App\Models\Ccrp\SenderInstruct;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Client;

class SenderInstructsController extends Controller
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
            ->header('500主机下位机绑定')
            ->description('description')
            ->body($this->grid());
    }

    public function lists(Sender $sender,Content $content)
    {

        $grid = new Grid(new SenderInstruct);

        $grid->model()->where('sender_id',$sender->id);
        $grid->model()->orderBy('id','desc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->sender()->note('名称');
        $grid->bindcode('绑定类型')->using(SenderInstruct::BINDCODES);
        $grid->bindss('绑定探头');
        $grid->senderid('绑定主机');
        $grid->created_at('下发时间');
        $grid->disableActions();

        return $content
            ->header('500主机下位机绑定')
            ->description($sender->id.','.$sender->note.''.$sender->sender_id)
            ->body($grid);
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
    public function create_sender(Sender $sender,Content $content)
    {
        $collectors = Collector::where('company_id',$sender->company->id)->where('status',1)->pluck('collector_name', 'supplier_collector_id');
        $form = new Form(new SenderInstruct);
        $form->setAction(route('ccrp.sender_instruct.crteated_sender',['sender'=>$sender->id]));
        $form->hidden('company_id', 'Company id')->default($sender->company_id);
        $form->hidden('sender_id', '编号 id')->default($sender->id)->readOnly();
        $form->text('senderid', '序列号')->default($sender->sender_id)->readOnly();
        $form->text('sender_name', '主机名称')->default($sender->note)->readOnly();
        $form->radio('bindcode', '绑定类型')->options(SenderInstruct::BINDCODES);
        $form->checkbox('bindss', '绑定探头')->options($collectors);
        $form->hidden('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->hidden('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $content
            ->header('Create')
            ->description('description')
            ->body($form);
    }

    public function created_sender(Sender $sender,Request $request)
    {
        $senderInstruct = new SenderInstruct();
        $senderInstruct->company_id = $request->company_id;
        $senderInstruct->sender_id = $request->sender_id;
        $senderInstruct->senderid = $request->senderid;
        $senderInstruct->bindcode = $request->bindcode;
        $sensers = $request->bindss;
        foreach ($sensers as $key=> $senser)
        {
            if($senser=='')
            {
                unset($sensers[$key]);
            }
        }
        $senderInstruct->bindss = implode('@',$sensers);
        $senderInstruct->created_at = $request->created_at;
        $senderInstruct->updated_at = $request->updated_at;
        $senderInstruct->save();
        $rscode =  $senderInstruct->instruct();
        admin_toastr('保存成功,已下发指令：'.$rscode);
        return redirect(  route('ccrp.sender_instruct', $request->sender_id) );
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SenderInstruct);
        $grid->model()->orderBy('id','desc');
        $grid->id('Id');
        $grid->company()->title('单位名称');
        $grid->sender()->note('名称');
        $grid->bindcode('绑定类型')->using(SenderInstruct::BINDCODES);
        $grid->bindss('绑定探头');
        $grid->senderid('绑定主机');
        $grid->created_at('下发时间');

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
        $show = new Show(SenderInstruct::findOrFail($id));

        $show->id('Id');
        $show->sender_id('Sender id');
        $show->bindcode('Bindcode');
        $show->bindss('Bindss');
        $show->senderid('Senderid');
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
        $form = new Form(new SenderInstruct);

        $form->number('sender_id', 'Sender id');
        $form->switch('bindcode', 'Bindcode');
        $form->text('bindss', 'Bindss');
        $form->text('senderid', 'Senderid');
        $form->number('company_id', 'Company id');
        $form->datetime('created_at', 'Created at')->default(date('Y-m-d H:i:s'));
        $form->datetime('updated_at', 'Updated at')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
