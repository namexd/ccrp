<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class BuildTask extends AbstractTool
{

    public function script()
    {
        return <<<EOT
        $('#build_task').on('click',function(){
        $(this).button('loading')
        $.ajax({
        method: 'get',
        url: '{$this->grid->resource()}/build_task',
        success: function (res) {
        if(res.code==1)
        {
         $.pjax.reload('#pjax-container');
            toastr.success('执行成功');
        }else
        {
         $.pjax.reload('#pjax-container');
            toastr.error('执行失败');
        }
        },
        error:function()
        {
          $.pjax.reload('#pjax-container');
            toastr.error('执行失败');
        }
    });
         
        });
    

EOT;

    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        Admin::script($this->script());
        return view('admin.tools.build_task');

    }
}