<?php

namespace App\Models\Ccrp;

use stdClass;

class Setting extends Coldchain2Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'slug', 'value', 'group', 'type', 'options', 'tip', 'sort',
        'object',
        'object_method',
        'object_key',
        'check_route',
        'set_route',
        'status'
    ];

    const TYPES = [
        'text' => '文本 text',
        'textarea' => '文字 textarea',
        'checkbox' => '多选 checkbox',
        'select' => '单选 select',
        'num' => '数字 num',
        'array' => '数组 array',
    ];
    const GROUPS = [
        '0' => '使用单位设置',
        '1' => 'CDC管理单位设置',
    ];
    const STATUSES = [
        '0' => '禁用',
        '1' => '正常',
    ];

    public function checkObject($object_value)
    {
        $check = $this;
        $model = 'App\\Models\\' . $check->object;
        $method = $check->object_method;
        $object = new $model;
        $result = $object->$method($object_value, $check);
        return $result;
    }

}
