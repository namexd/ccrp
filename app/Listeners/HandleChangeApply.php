<?php

namespace App\Listeners;

use App\Events\AutoHandleApply;
use App\Jobs\SendMessage;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\EquipmentChangeApply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class HandleChangeApply implements ShouldQueue
{

    public function handle(AutoHandleApply $event)
    {
        $apply = $event->getApply();
        $apply->load('details');
        foreach ($apply->details as $detail) {
            if (in_array($detail->change_type, [EquipmentChangeApply::冷链设备关闭报警, EquipmentChangeApply::冷链设备开通报警])) {//自动处理
                $status = $detail->change_type == EquipmentChangeApply::冷链设备关闭报警 ? 0 : 1;
                $cooler = Cooler::find($detail->cooler_id);
                DB::transaction(function () use ($detail,$cooler,$status) {
                $result=$cooler->setWarningByStatus($status);
                    if ($result['code'])
                    {
                        $auto['is_auto'] = 1;
                        $auto['comment'] = '系统自动处理';
                        $auto['status'] = EquipmentChangeApply::状态_处理完成;
                    }else
                    {
                        $auto['comment']=$result['message'];
                        $this->sendMessage($detail->apply);
                    }
                    $detail->apply()->update($auto);
                });
            } else//发送客服处理
            {
                $this->sendMessage($detail->apply);
            }
        }

    }

    public function sendMessage($apply)
    {
        $message = [
            'subject' => '【'.$apply->company->title.'】有新的冷链变更单申请',
            'content' => '有新的冷链变更单申请,请登录CCSC后台处理',
            'message_type' => '5',
            'content_detail' => [
                'number' => $apply->id,
                'status' => '未处理',
                'handler' => '客服',
                'remark' => '申请单位：'.$apply->company->title
            ],
            'from_type' => '3',
            'send_time' => time(),
            'app_id' => 3,
            'app_user_id' => '2,3,9'
        ];
        dispatch(new SendMessage($message));
    }
}
