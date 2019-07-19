<?php

namespace App\Models\Ccrp;


class Area extends Coldchain2Model
{
    protected $table = 'area';
    protected $fillable = [
        'id',
        'name',
        'parent_id',
        'short_name',
        'level_type',
        'city_code',
        'zip_code',
        'merger_name',
        'lng',
        'lat',
        'pinyin',
        'status',
        'order',
        'count_company',
        'count_company_ll',
        'count_company_ll2',
        'count_company_swzp',
        'count_warning',
    ];

    public static function getListByConditions($where)
    {
        $result = [];
        $builder = self::query()->where('status', 1);

        if ($where) {

            $builder = $builder->where($where);

        }

        $list = $builder->get();
        //转换成一维数组

        foreach ($list as $val) {

            $result[] = ['value' => $val['id'], 'label' => $val['name']];

        }

        return $result;
    }

   public static function get_area_pinyin($id)
    {
        $result = self::find($id);
        return strtolower($result['pinyin']);

    }


}
