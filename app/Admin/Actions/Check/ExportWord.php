<?php

namespace App\Admin\Actions\Check;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ExportWord extends RowAction
{
    public $name = '导出Word';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }
    public function href()
    {
        return route('check_task.export_word',$this->row->id);
    }

}
