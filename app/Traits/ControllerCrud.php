<?php

namespace App\Traits;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;

trait ControllerCrud
{
    public $crudModel = null;

    /**
     * 绑定模型
     * @return null
     */
    public function crudModel()
    {
        return null;
    }

    public function setCrudModel($model)
    {
        $this->crudModel = $model;
    }

    public function getCrudModel()
    {
        return $this->crudModel;
    }

    /**
     * @param $function string filter,columns
     * @param null $with
     * @return bool
     */
    public function withMeta($function, $with = null)
    {
        $with = $with ?? request()->get('with');
        if ($with) {
            $withs = explode(',', $with);
            if (in_array($function, $withs)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 展示代码
     * @param $rs
     * @param null $with
     * @return mixed
     */
    public function display($rs, $with = null)
    {
        if ($this->getCrudModel() == null) {
            return $rs;
        }
        if ($this->withMeta('filter', $with)) {
            $rs->addMeta('filter', $this->withFilter());
        }
        if ($this->withMeta('columns', $with)) {
            $rs->addMeta('columns', $this->withColumns());
        }
        if ($this->withMeta('toolbar', $with)) {
            $rs->addMeta('toolbar', $this->withToolbar());
        }
        if ($this->withMeta('table', $with)) {
            $rs->addMeta('table', $this->withTable());
        }
        if ($this->withMeta('options', $with)) {
            $rs->addMeta('filter', $this->withFilter());
            $rs->addMeta('columns', $this->withColumns());
            $rs->addMeta('toolbar', $this->withToolbar());
            $rs->addMeta('table', $this->withTable());
        }
        return $rs;
    }

    /**
     * @param string $function
     * @param string $action
     * @param string $innerText
     * @param string $type
     * @param string $size
     * @param string $icon
     * @return array
     */
    public function toolBarAddButton($function = "submit", $action = null, $innerText = null, $type = null, $size = "small", $icon = '')
    {
        switch ($function) {
            case 'add':
                $innerText = $innerText ?? "新增";
                $type = $type ?? 'success';
                $action = $action ?? 'add';
                break;
            case 'edit':
                $innerText = $innerText ?? "编辑";
                $type = $type ?? 'danger';
                $action = $action ?? 'edit';
                break;
            case 'link':
                $innerText = $innerText ?? "打开";
                $type = $type ?? 'text';
                $action = $action ?? 'http://www.baidu.com';
                break;
            case 'submit':
                $innerText = $innerText ?? "提交";
                $type = $type ?? 'primary';
                break;
            case 'print':
                $innerText = $innerText ?? "打印";
                $type = $type ?? 'success';
                break;
            case 'excel':
                $innerText = $innerText ?? "导出Excel";
                $type = $type ?? 'danger';
                break;
            case 'pdf':
                $innerText = $innerText ?? "导出Pdf";
                $type = $type ?? 'danger';
                break;
            default:
                $innerText = $innerText ?? "确认";
                $type = $type ?? 'primary';
        }
        return $button = [
            //自定义
            'function' => $function,
            //动作或者url
            'action' => $action,
            //按钮类型，可选值为primary、ghost、dashed、text、info、success、warning、error或者不设置
            'type' => $type,
            //按钮大小，可选值为large、small、default或者不设置
            'size' => $size,
            //按钮文字提示
            'innerText' => $innerText,
            //设置按钮的图标类型
            'icon' => $icon,
            //设置按钮为加载中状态
            'loading' => 'false',
            //按钮形状，可选值为circle或者不设置
            'shape' => 'undefined',
            //设置button原生的type，可选值为button、submit、reset
            'htmlType' => "button",
            //开启后，按钮的长度为 100%
            'long' => 'false',
            //设置按钮为禁用状态
            'disabled' => 'false',
        ];
    }

    public function toolbarButtons()
    {
        return [];
    }

    public function tableButtons()
    {
        return [];
    }

    public function tableSortable()
    {
        return [];
    }

    public function withFilter()
    {
        return $this->getCrudModel() ? $this->getCrudModel()::filter() : null;
    }

    public function withColumns()
    {
        if ($this->getCrudModel()) {
            return $columns = $this->getCrudModel()::columns();
        }
        return null;

    }

    public function withToolbar()
    {
        return [
            'buttons' => $this->toolbarButtons()
        ];
    }

    public function withTable()
    {
        $res['sortable'] = $this->tableSortable();
        $res['buttons'] = $this->tableButtons();
        return $res;
    }
}
