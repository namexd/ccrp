<?php

namespace App\Admin\Actions\Check;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class ResetCheck extends BatchAction
{
    public $name = '批量重置';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->status=0;
            $model->save();
        }

        return $this->response()->success('操作成功')->refresh();
    }

}