<?php

namespace App\Traits;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;

trait OrderColumn
{

    public function setOrder($model)
    {
        if ($order = request()->get('order', '')) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 根据传入的排序值来构造排序参数
                $model->orderBy($m[1], $m[2]);
            }
        }
        return $model;
    }

}
