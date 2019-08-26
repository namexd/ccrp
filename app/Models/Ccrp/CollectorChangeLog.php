<?php

namespace App\Models\Ccrp;

use App\Traits\ModelFields;
use function App\Utils\dateFormatByType;
use PrinterAPI;

class CollectorChangeLog extends Coldchain2Model
{
    protected $table = 'collector_changelog';
    protected $fillable = [
        'collector_id',
        'collector_name',
        'cooler_id',
        'cooler_name',
        'supplier_id',
        'supplier_collector_id',
        'new_supplier_collector_id',
        'category_id',
        'company_id',
        'change_note',
        'change_time',
        'change_option',
    ];

    public function collector()
    {
        return $this->belongsTo(Collector::class, 'collector_id', 'collector_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    public function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }
    //巡检报告-监测设备维护统计表（报废）
    public function getUselessCollector($company_id, $date = '')
    {

        $company_ids = Company::find($company_id)->ids(0);
        $bfIds=$this->where('change_option',0)->pluck('supplier_collector_id');
        return $this->where('change_option', 1)
            ->whereBetween('change_time', [$date['start'], $date['end']])
            ->whereIn('company_id', $company_ids)
            ->whereNotIn('supplier_collector_id',$bfIds)
            ->with(['company' => function ($query) {
                $query->selectRaw('id,title');
            },'cooler'=>function($query){
                $query->selectRaw('cooler_id,cooler_sn');
            }])
            ->selectRaw('cooler_id,company_id,change_time,supplier_collector_id')
            ->get()
            ->toArray();
    }
    //巡检报告-监测设备维护统计表（更换）
    public function getChangeCollector($company_id, $date = '')
    {

        $company_ids = Company::find($company_id)->ids(0);
        return $this->where('change_option', 0)
            ->whereBetween('change_time', [$date['start'], $date['end']])
            ->whereIn('company_id', $company_ids)
            ->with(['company' => function ($query) {
                $query->selectRaw('id,title');
            },'cooler'=>function($query){
                $query->selectRaw('cooler_id,cooler_sn');
            }])
            ->selectRaw('cooler_id,company_id,change_time,supplier_collector_id,new_supplier_collector_id')
            ->get()
            ->toArray();
    }
}
