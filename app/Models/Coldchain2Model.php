<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coldchain2Model extends Model
{
    protected $connection = 'dbyingyong';

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function getCreatedAtColumn()
    {
        return null;
    }

    public function setUpdatedAt($value)
    {
        return null;
    }

    public function setCreatedAt($value)
    {
        return null;
    }
}
