<?php

namespace App\Admin\Actions\User;

use App\Models\Ccrp\Company;
use function App\Utils\loginkey;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class AdminLogin extends RowAction
{
    public $name = '变身登录';

    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success('Success message.')->refresh();
    }

    public function href()
    {
        $user = $this->row;
        if ($user->binding_domain) {
            $domain = Company::ONLINE_DOMAIN;
            $domain_pre = 'http://' . $domain[$user->binding_domain] . '/';
        } else {
            $domain_pre = 'http://www2.coldyun.com/';
        }
        $url = $domain_pre . '/user/admin_login?cdc_admin=1&key=' . loginkey() . '&id=' . $user['id'];
        return $url;
    }

}
