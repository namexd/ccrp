<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Controllers\Api\Ccrp\Controller as CcrpController;

class Controller extends CcrpController
{
    public $company_id = null;

    public function __construct()
    {
        if (isset(request()->all()['company_id'])) {
            $this->company_id = request()->company_id;
        }
        parent::__construct();
    }
}
