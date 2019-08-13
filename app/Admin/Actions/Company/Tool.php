<?php

namespace App\Admin\Actions\Company;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Tool extends RowAction
{
    public $name = '操作与设置';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    public function href()
    {
        return route('ccrp.company.tools', ['id'=>$this->row->id]) ;
    }

}
