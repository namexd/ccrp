<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class UpdateField extends BatchAction
{
    protected $route;
    protected $field;
    protected $value;

    public function __construct($route,$field = 1,$value)
    {
        $this->route = $route;
        $this->field = $field;
        $this->value = $value;
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
            field: '{$this->field}',
            value: '{$this->value}',
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