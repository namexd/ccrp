<?php

namespace App\Models\Ccrp;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Area extends Coldchain2Model
{

    use AdminBuilder, ModelTree {
        ModelTree::boot as treeBoot;
    }
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

    public function parent()
    {
        return $this->hasOne(self::class,'id','parent_id');
    }
    function companies()
    {
        return $this->hasMany(Company::class,'region_code','id');
    }
    function adminCompany()
    {
        return $this->hasMany(Company::class,'region_code','id')->where('cdc_admin','=',1);
    }

    /**
     * @return array
     */
    public function allNodes(): array
    {
        return static::with('adminCompany')->get()->toArray();
    }


}
