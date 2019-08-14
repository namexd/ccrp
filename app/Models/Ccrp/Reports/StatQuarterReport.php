<?php

namespace App\Models\Ccrp\Reports;

use App\Models\Ccrp\Coldchain2Model;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;

class StatQuarterReport extends Coldchain2Model
{
    protected $table = 'stat_quarter_report';
    protected $fillable = [
        'company_id',
        'company',
        'title',
        'type',
        'year',
        'season',
        'num_companys',
        'start',
        'end',
        'content',
        'note',
        'create_time',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cooler()
    {
        return $this->belongsTo(Cooler::class);
    }


}
