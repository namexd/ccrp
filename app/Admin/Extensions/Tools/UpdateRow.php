<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class UpdateRow extends BatchAction
{
    protected $route;
    protected $function;
    protected $oprate;

    public function __construct($route, $function = '', $oprate = '')
    {
        $this->route = $route;
        $this->function = $function;
        $this->oprate = $oprate;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '{$this->route}',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            function: '{$this->function}',
            oprate: '{$this->oprate}',
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });
});

EOT;

    }
}