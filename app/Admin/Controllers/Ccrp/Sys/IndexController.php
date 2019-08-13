<?php

namespace App\Admin\Controllers\Ccrp\Sys;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Table;

class IndexController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {


        // 选填
        $content->header('冷链系统参数清单');

        // 选填
        $content->description('-');

        // 添加面包屑导航 since v1.5.7
        $content->breadcrumb(
            ['text' => '首页', 'url' => '/'],
            ['text' => '冷链系统参数清单']
        );

        $headers = ['名称', '操作'];
        $rows = [
            [ '1. <a href="'.'/admin/ccrp/sys/settings">系统参数</a>',''],
            [ '2. <a href="'.'/admin/ccrp/sys/company/details">单位详情设置</a>',''],
            [ '3. <a href="'.'/admin/ccrp/sys/company/photos">单位照片设置</a>',''],
            [ '4. <a href="'.'/admin/ccrp/sys/cooler/details">冷链详情设置</a>',''],
            [ '5. <a href="'.'/admin/ccrp/sys/company/photos">冷链装备照片设置</a>',''],
            [ '6. <a href="'.'/admin/ccrp/sys/cooler_brands">冰箱品牌</a>',''],
            [ '7. <a href="'.'/admin/ccrp/sys/cooler_models">冰箱型号</a>',''],
        ];

        $table = new Table($headers, $rows);


        $content->row($table->render());


        return $content;
    }

}
