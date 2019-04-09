<?php
namespace App\Models;
class CoolerCategory extends Coldchain2Model
{
    protected $table = 'cooler_category';
    protected $primaryKey = 'id';

    protected $fillable =['id', 'pid', 'group', 'cooler_type', 'title','cooler_count','cooler_sum', 'ctime', 'cuid', 'utime', 'sort', 'status', 'company_id'];
}
