<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class WechatMedia2Topic extends BatchAction
{
    protected $route;
    protected $field;
    protected $value;

    public function __construct()
    {
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: 'wechat_medias/move2topic',
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