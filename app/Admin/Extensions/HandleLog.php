<?php

namespace App\Admin\Extensions;

use App\Models\HumanCheckLogs;
use Encore\Admin\Admin;

class HandleLog
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    protected function script()
    {
        return <<<SCRIPT
$('.history').on('click',function(){
 $("#table_history").toggle();
});
SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        $company_id = $this->model->object_value;
        $lists = HumanCheckLogs::where('company_id', $company_id)->get();
        return view('admin.tools.handle_log', ['model' => $this->model, 'lists' => $lists]);
    }

    public function __toString()
    {
        return $this->render()->render();
    }
}