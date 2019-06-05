<?php

namespace App\Models\Ccrp;
class File extends Coldchain2Model
{
    const CATEGORIES = [
        '第三方校准证书' => '第三方校准证书',
        '校准证书' => '校准证书',
        '合格证' => '合格证',
        '公司资质' => '公司资质'
    ];
    protected $fillable = [
        'id',
        'file_name',
        'file_server',
        'file_url',
        'file_type',
        'file_category',
        'file_desc',
        'company_id',
        'company_name',
        'create_time',
        'out_date',
        'file_url2',
        'status',
        'note',
    ];

    public function url()
    {
        return $this->file_server . '' . $this->file_url;
    }
}
