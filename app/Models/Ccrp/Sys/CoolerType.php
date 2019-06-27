<?php
namespace App\Models\Ccrp\Sys;
use App\Models\Ccrp\Coldchain2Model;

class CoolerType extends Coldchain2Model
{
    protected $table = 'sys_cooler_types';
    protected $primaryKey = 'id';

    protected $fillable =[
        'id',
        'name',
        'category',
        'slug',
        'description',
        'note'
    ];
}
