<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;

class Coldchain2pgModel extends Model
{
    protected $connection = 'pgyingyong';

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
