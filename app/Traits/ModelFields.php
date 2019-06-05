<?php

namespace App\Traits;

use App\Helpers\FormCreateHelper;
use LaravelFormBuilder\Form;

trait ModelFields
{
    protected $filters = [];

    /**
     * 列表显示字段，为空则为全部
     * @return array
     */
    protected static function columnsFields()
    {
        return null;
    }
    /**
     * 生成过滤器的字段
     * @return array
     */
    protected static function filterFields()
    {
        return [];
    }

    /**
     * 字段名称
     * @return array
     */
    protected static function fieldTitles()
    {
        return [];
    }

    /**
     * 字段Trans的文件名
     * @return String
     */
    protected static function fieldTransFile()
    {
        return 'common';
    }


    /**
     * 获取字段中文名称
     * @param $field
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    protected static function getFieldTitle($field)
    {
        if (isset(self::fieldTitles()[$field])) {
            return self::fieldTitles()[$field];
        } else {
            $trans_str = self::fieldTransFile() . '.' . $field;
            $trans_res = trans($trans_str);
            return ($trans_res == $trans_str) ? $field : $trans_res;
        }
    }

    /**
     * 获取字段中文名称（数组）
     * @param $fields
     * @return array
     */
    protected static function getFieldsTitles($fields)
    {
        $result = [];
        $model_titles = self::fieldTitles();
        foreach ($fields as $key => $field) {
            if (isset($model_titles[$field])) {
                $result[$field] = $model_titles[$field];
            } else {
                $trans_str = self::fieldTransFile() . '.' . $field;
                $trans_res = trans($trans_str);
                $result[$field] = ($trans_res == $trans_str) ? $field : $trans_res;
            }
        }
        return $result;
    }

    protected static function addFilter($type, $name, $options = [])
    {
        $label = $options['label'] ?? self::getFieldTitle($name);
        $item = null;
        switch ($type) {
            case 'text':
                $item = Form::input($name, $label);
                break;
            case 'password':
                $item = Form::password($name, $label);
                break;
            default:
                $item = 'default...';;
        }
        return $item;
    }

    public static function filter()
    {
        $items = [];
        foreach (self::filterFields() as $key => $value) {
            if (is_array($value)) {
                $items[] = self::addFilter($value[0], $key, $value['options']);
            } elseif (is_string($value)) {
                $items[] = self::addFilter($value, $key);
            }
        }
        //input组件
        $items['name'] = Form::input('name', '用户名');
        $items['password'] = Form::password('password', '密码');
        //日期区间选择组件
        $items['date_range'] = Form::dateRange(
            'date_range',
            '区间日期',
            strtotime('- 10 day'),
            time()
        );
        $checkbox_options = [
            ['value' => '1', 'label' => '选项一', 'disabled' => false],
            ['value' => '2', 'label' => '选项二', 'disabled' => true]
        ];
        $items['check_box'] = Form::checkbox('categories', '表单', [])->options($checkbox_options)->col(Form::col(12));

        $select_options = [
            ['value' => 'red', 'label' => '红色'],
            ['value' => 'blue', 'label' => '蓝色']
        ];
        $items['select'] = Form::select('color', '颜色', [])->options($select_options);

        $form = new FormCreateHelper('', $items);
        $api = $form->buildFilter();
        return $api;
    }

    public static function columns()
    {
        $columns =self::getFieldsTitles(self::columnsFields()??(new self)->getFillable());
        $res = [];
        foreach($columns as $key=>$column)
        {
            $res[] = ['label'=>$column,'value'=>$key];
        }
        return $res;
    }
}
