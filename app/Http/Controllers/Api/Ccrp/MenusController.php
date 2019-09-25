<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Menu;
use App\Models\Ccrp\MenuCommon;
use App\Models\Ccrp\MenuCompany;
use App\Models\Ccrp\MenuRole;
use App\Models\Ccrp\MenuUSer;
use App\Models\Ccrp\Role;
use Illuminate\Http\Request;

class MenusController extends Controller
{

    public function index()
    {
        $this->check();
        $role = $this->company->cdc_admin ? 'cdc' : 'unit';
        $commonMenu = MenuCommon::pluck('menu_id')->toArray();
        $companyMenu = MenuCompany::where('company_id', $this->user->company_id)->pluck('menu_id')->toArray();
        $role_id=Role::where('role',$role)->first()->id;
        $roleMenu = MenuRole::where('role_id', $role_id)->pluck('menu_id')->toArray();
        $userMenu = MenuUSer::where('status', 1)->where('user_id', $this->user->id)->pluck('menu_id')->toArray();
        $combine_menus = array_keys(
            array_flip($commonMenu)
            + array_flip($companyMenu)
            + array_flip($roleMenu)
            + array_flip($userMenu));
        $menus = Menu::whereIn('id', $combine_menus)->where('status',1)->whereRaw('length(slug)>0')->pluck('slug');
        return $this->response->array($menus);

    }


}
