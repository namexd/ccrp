<?php

namespace App\Admin\Actions\Check;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ExportPDF extends RowAction
{
    public $name = '导出PDF';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }
    public function href()
    {
        return route('check_task.export_pdf',$this->row->id);
    }

}
