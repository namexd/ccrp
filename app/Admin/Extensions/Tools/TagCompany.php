<?php

namespace App\Admin\Extensions\Tools;

use App\Models\Ccrp\Tag;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;

class TagCompany extends AbstractTool
{
    protected $action;

    public function __construct($action = 1)
    {
        $this->action = $action;
    }

    public function script()
    {
        $data=json_encode(Tag::select('id','name as text')->get()->toArray());
        return <<<EOT
var data = {$data};
$("#tags").select2({
 data: data,
 placeholder:'请选择',
 allowClear:true,
 multiple: true
})
$('#set_tag').on('click',function(){
if(selectedRows().length==0)
{
 toastr.error('请选择单位');
 return false;
}
});
$('#save_tags').on('click', function() {
var reslist=$("#tags").val()
if(!reslist)
{
 toastr.error('请选择标记');
 return false;
}


    $.ajax({
        method: 'post',
        url: '{$this->grid->resource()}/tag',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            tags: reslist
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            $("#myModal").modal('hide')
            toastr.success('操作成功');
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
        return view('admin.tools.tag_company');

    }
}