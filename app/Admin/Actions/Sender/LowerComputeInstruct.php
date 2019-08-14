<?php

namespace App\Admin\Actions\Sender;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class LowerComputeInstruct extends RowAction
{
    public $name = '下位机指令';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    public function href()
    {
        return route('ccrp.sender_instruct', $this->row->id) ;
    }

}
