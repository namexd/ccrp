<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\UsersLoginRequest;
use App\Models\Ccrp\User;
use App\Models\Ocenter\WxMember;
use App\Transformers\Ccrp\Ocenter\WxMemberTransformer;

class UsersController extends Controller
{

    public function login(UsersLoginRequest $request)
    {
        $check = (new User)->checkPassword($request->username,$request->password);
        if($check)
        {
            return $this->response->array($check->toArray());
        }
        return $this->response->error('用户名或者密码错误',401);
    }

    public function weixinMember()
    {
        $this->check();
        $model=new WxMember();
        if ($keyword = request()->get('keyword')) {
            $model = $model->where(function ($query) use ($keyword){
                $query->where('nickname', 'like', '%'.$keyword.'%')->orWhere('truename', 'like', '%'.$keyword.'%');
            });
        }
        if ($this->user['userlevel'] == 2) {
            $company_users = [$this->user['username']];
        } else {
            $company_users = $this->company_users($this->company->id);
        }
        $map['status'] = 1;
        $data=$model->whereIn('username',$company_users)
            ->whereIn('app_id',[2,3,4])
            ->where('status',1)
            ->paginate(request()->get('pagesize',$this->pagesize));
        return $this->response->paginator($data,new WxMemberTransformer());
    }

    public function updateWxMember($id)
    {
        $this->check();
        $update=request()->all();
        $model=new WxMember();
        $member=$model->find($id);
        $member->update($update);
        return $this->response->item($member,new WxMemberTransformer());
    }

    public function unbindWxmember($id)
    {
        $model=new WxMember();
        $map['id'] = $id;
        $map['status'] =  1;
        $weixin = $model->where($map)->whereIn('app_id',[2,3])->first();

        if(!$weixin){
         return   $this->response->errorBadRequest('已经解绑了哦');
        }
        $weixin->update(['status'=>0]);
        return $this->response->noContent();
    }

    public function company_users($this_company_id = 0)
    {

        $con['company_id'] = $this_company_id;
        $users =User::query()->select('username')->where($con)->where('username','<>','1234')->get();
        for ($i = 0; $i < count($users); $i++) {
            $aa[$i] = $users[$i]['username'];
        }
        return $aa;
    }
}
