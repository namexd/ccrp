<?php

namespace App\Admin\Actions\Company;

use App\Models\Ccrp\Company;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class AdminLogin extends RowAction
{
    public $name = '变身登录';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    public function href()
    {
        return route('ccrp.login',$this->row->id);
    }

}
