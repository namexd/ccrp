<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\MenuCommon;
use App\Models\Ccrp\MenuCompany;
use App\Models\Ccrp\MenuRole;
use App\Models\Ccrp\MenuUSer;
use Illuminate\Http\Request;

class MenusController extends Controller
{

    public function index()
    {
        $this->check();
        $role= $this->company->cdc_admin?'cdc':'unit';
        $commonMenu=MenuCommon::where('status',1)->pluck('slug');
        $companyMenu=MenuCompany::where('status',1)->where('company_id',$this->user->company_id)->pluck('slug');
        $roleMenu=MenuRole::where('status',1)->where('role',$role)->pluck('slug');
        $userMenu=MenuUSer::where('status',1)->where('user_id',$this->user->id)->pluck('slug');
        $menus=  array_keys(array_flip($commonMenu)+array_flip($companyMenu)+array_flip($roleMenu)+array_flip($userMenu));
        return $this->response->array($menus);

    }


}
