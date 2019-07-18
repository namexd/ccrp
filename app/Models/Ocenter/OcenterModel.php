<?php

namespace App\Models\Ocenter;

use Illuminate\Database\Eloquent\Model;

class OcenterModel extends Model
{
    protected $connection = 'dbocenter';

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
