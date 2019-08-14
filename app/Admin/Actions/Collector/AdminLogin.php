<?php

namespace App\Admin\Actions\Collector;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class AdminLogin extends RowAction
{
    public $name = '变身登陆';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }
    public function href()
    {
        return route('ccrp.login',$this->row->company_id);
    }

}
