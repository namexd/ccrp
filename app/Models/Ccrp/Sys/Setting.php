<?php

namespace App\Models\Ccrp\Sys;

use App\Models\Ccrp\Coldchain2Model;
use App\Traits\ModelFields;

class Setting extends Coldchain2Model
{
    use ModelFields;
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
        '0' => '使用账号',
        '1' => '管理账号',
    ];
    const STATUSES = [
        '0' => '禁用',
        '1' => '正常',
    ];


    const  CATEGORIES= [
        'company'=>'单位设置',
        'warninger'=>'报警通道',
        'warning_setting'=>'预警设置',
        'report'=>'报表设置',
        'other'=>'其他',
    ];

    protected static function columnsFields()
    {
        return [
            'category',
            'name',
            'slug',
            'value',
            'group',
            'type',
            'tip',
            'options',
        ];
    }

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
