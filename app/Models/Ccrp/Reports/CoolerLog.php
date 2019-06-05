<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Cooler;
use App\Traits\ModelFields;

class CoolerLog extends Coldchain2Model
{
    use ModelFields;
    protected $table='cooler_log';

    public function getStatusAttribute($value)
    {
        return Cooler::$status[$value];
    }
    public function getCompanyAttribute()
    {
        return $this->coolers->company->title;
    }
    public function getNoteTimeAttribute($value)
    {
        return date('Y-m-d H:i:s',$value);
    }
    public function coolers()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }

    public function getListByDate($companyIds,$start,$end)
    {
        return $this->whereHas('coolers',function ($query) use($companyIds){
            $query->whereIn('company_id',$companyIds);
        })->whereBetween('note_time',[$start,$end])->orderBy('id','desc');
    }
    static public function fieldTitles()
    {
        return[
            'company'=>'单位',
            'cooler_name'=>'设备名称',
            'cooler_sn'=>'设备ID',
            'status'=>'设备状态',
            'note'=>'备注',
            'name'=>'操作人',
            'note_time'=>'操作时间',
        ];
    }
}
