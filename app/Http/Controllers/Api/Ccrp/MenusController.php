<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Menu;
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
        $commonMenu=MenuCommon::pluck('menu_id');
        $companyMenu=MenuCompany::where('company_id',$this->user->company_id)->pluck('menu_id');
        $roleMenu=MenuRole::where('role',$role)->pluck('menu_id');
        $userMenu=MenuUSer::where('status',1)->where('user_id',$this->user->id)->pluck('menu_id');
        $combine_menus=  array_keys(array_flip($commonMenu)+array_flip($companyMenu)+array_flip($roleMenu)+array_flip($userMenu));
        $menus=Menu::whereIn('id',$combine_menus)->pluck('slug');
        return $this->response->array($menus);

    }


}
