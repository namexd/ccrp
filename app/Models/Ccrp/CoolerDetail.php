<?php
namespace App\Models\Ccrp;

use App\Models\Ccrp\Sys\SysCoolerDetail;

class CoolerDetail extends Coldchain2ModelWithTimestamp
{
    protected $table = 'cooler_details';

    protected $fillable =[
        'cooler_id',
        'company_id',
        'sys_id',
        'value',
    ];
    function cooler()
    {
        return $this->belongsTo(Cooler::class,'cooler_id','cooler_id');
    }
    public function sys_detail()
    {
        return $this->belongsTo(SysCoolerDetail::class, 'sys_id', 'id');
    }

}
