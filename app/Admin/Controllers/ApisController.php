<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ccrp\Company;
use DB;

class ApisController extends Controller
{
    public function company()
    {
        $q = request()->get('q');
        return Company::where('title', 'like', "%$q%")
            ->orderBy('cdc_admin', 'desc')
            ->orderBy('region_code', 'asc')
            ->orderBy('company_group', 'asc')
            ->orderBy('cdc_level', 'asc')
            ->orderBy('pid', 'asc')
            ->orderBy('sort', 'asc')
            ->orderBy('company_type', 'asc')
            ->orderBy('username', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(null, ['id', DB::raw('concat(title," ( ",username,"--",region_name," )") as text')]);
    }
}
