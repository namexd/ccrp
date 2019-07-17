<?php

namespace App\Models\Ccrp;

/**
 * Class Collector
 * @package App\Models
 */
class Product extends Coldchain2Model
{
    protected $table = 'product';
    protected $primaryKey = 'product_id';

    const TYPE=[
        '探头','报警器','一体机','中继器'
    ];

}
