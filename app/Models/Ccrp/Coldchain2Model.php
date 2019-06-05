<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;

class Coldchain2Model extends Model
{
    protected $connection = 'dbyingyong';
    private $api_server = '';

    public function setApiServer($app)
    {
        $this->api_server =$app->api_auth_url;
    }
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
