<?php


namespace App\Models\Ccrp;

use App\Models\UserHasApp;
use App\Models\Weapp;

class UserLoginLog extends Coldchain2Model
{
    protected $table = 'user_login_log';

    public static function addCcrpLoginLog($request,$user)
    {
        $apps = $user->apps->where('slug', 'ccrp');
        $apps = current($apps);
        $app =current($apps);
        if ($app) {
            $user_app = UserHasApp::where('user_id', $user->id)->where('app_id', $app->id)->first();
            if ($user_app) {
                $wxcode = $user->weappHasWeuser->where('weapp_id',Weapp::壹苗链小程序);
                $wxcode = current($wxcode);
                $wxcode = current($wxcode);
                $ccrp_user = User::where('id', $user_app->app_userid)->where('status', 1)->first();
                self::addLog($ccrp_user->id, $ccrp_user->username, $ccrp_user->userlevel, $ccrp_user->company_id, 5, time(), $request->ip(), $user->realname,$wxcode->openid);
                self::addLogPg($ccrp_user->id, $ccrp_user->username, $ccrp_user->userlevel, $ccrp_user->company_id, 5, time(), $request->ip(), $user->realname,$wxcode->openid);
            }
        }

    }

    private static function addLog($uid, $username, $userlevel, $company_id, $type, $login_time, $ip, $note,$wxcode='')
    {
        $log = new self;
        $log->uid = $uid;
        $log->username = $username;
        $log->userlevel = $userlevel;
        $log->company_id = $company_id;
        $log->type = $type;
        $log->login_time = $login_time;
        $log->ip = $ip;
        $log->note = $note;
        $log->wxcode = $wxcode;
        $log->save();
    }

    private static function addLogPg($uid, $username, $userlevel, $company_id, $type, $login_time, $ip, $note,$wxcode='')
    {
        $log = new UserLoginLogPg();
        $log->uid = $uid;
        $log->username = $username;
        $log->userlevel = $userlevel;
        $log->company_id = $company_id;
        $log->type = $type;
        $log->login_time = $login_time;
        $log->ip = $ip;
        $log->note = $note;
        $log->wxcode = $wxcode;
        $log->save();
    }

}
