<?php

namespace App\Admin\Actions\Check;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class CheckTaskShow extends RowAction
{
    public $name = '预览';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }
    public function href()
    {
        return route('check_tasks.show',$this->row->id);
    }

}
