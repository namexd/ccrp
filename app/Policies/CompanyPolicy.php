<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function unit_operate(User $user, $company)
    {
        $bindApp = $user->hasApps->where('app_id', 1)->first();
        $ccrp_user = \App\Models\Ccrp\User::find($bindApp->app_userid);
        return $company->cdc_admin == 0 && in_array($company->id, $ccrp_user->userCompany->ids());
    }

}
