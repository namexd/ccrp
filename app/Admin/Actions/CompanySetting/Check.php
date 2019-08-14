<?php

namespace App\Admin\Actions\CompanySetting;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Check extends RowAction
{
    public $name = '检查设置';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    public function href()
    {
        return route($this->row->setting->check_route, [$this->row->setting_id, $this->row->company_id]);
    }

}
