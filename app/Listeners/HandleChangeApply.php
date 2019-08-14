<?php

namespace App\Listeners;

use App\Events\AutoHandleApply;
use App\Jobs\SendMessage;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Contact;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\EquipmentChangeApply;
use App\Models\Ccrp\Warninger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class HandleChangeApply implements ShouldQueue
{

    public function handle(AutoHandleApply $event)
    {
        $apply = $event->getApply();
        $apply->load('details');
        foreach ($apply->details as $detail) {
            $is_auto = 1;
            $auto['is_auto'] = 1;
            $auto['comment'] = '系统自动处理';
            $auto['status'] = EquipmentChangeApply::状态_处理完成;
            $cooler = Cooler::find($detail->cooler_id);
            switch ($detail->change_type) {
                case EquipmentChangeApply::冷链设备关闭报警:
                case EquipmentChangeApply::冷链设备开通报警:
                    $status = $detail->change_type == EquipmentChangeApply::冷链设备关闭报警 ? 0 : 1;
                    $result = $cooler->setWarningByStatus($status);
                    if ($result['count'] !== 0) {
                        $auto['comment'] = $result['message'];
                        $this->sendMessage($detail->apply);
                    }
                    break;
                case EquipmentChangeApply::冰箱备用:
                case EquipmentChangeApply::冰箱报废:
                case EquipmentChangeApply::冰箱启用:
                    $this->changeCoolerStatus($detail->change_type, $cooler);
                    break;
                case EquipmentChangeApply::取消探头:
                    $this->uninstallCollector($detail->collector_id);
                    break;
                case EquipmentChangeApply::报警联系人变更:
                    $this->changeWarningContact($detail->apply);
                    break;
                default :
                    $is_auto = 0;
                    $this->sendMessage($detail->apply);
                    break;
            }
            if ($is_auto) {
                $detail->apply()->update($auto);
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

    public function changeCoolerStatus($change_type, $cooler)
    {
        $status = 1;
        switch ($change_type) {
            case EquipmentChangeApply::冰箱备用:
                $status = Cooler::状态_备用;
                break;
            case EquipmentChangeApply::冰箱报废:
                $status = Cooler::状态_报废;
                break;
            case EquipmentChangeApply::冰箱启用:
                $status = Cooler::状态_正常;
                break;
        }
        (new Cooler())->ChangeCoolerStatus($cooler, $status, '冷链变更单', 0);
    }

    public function uninstallCollector($collector_ids)
    {
        $collector = new Collector();
        foreach ($collector_ids as $collector_id) {
            $collector->uninstall($collector_id, '冷链变更单');
        }
    }

    public function changeWarningContact($apply)
    {
        $contact = $apply->contact;
        switch ($contact->action) {
            case 'add':
                $create=Contact::create([
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'company_id' => $apply->company_id,
                    'create_time' => time(),
                    'create_uid'=>0,
                    'level'=>$contact->level??1,
                    'job'=>'',
                    'voice'=>'',
                    'note' => '冷链变更单新增'
                ]);
                if ($contact->warninger_id) {
                    $warninger = Warninger::find($contact->warninger_id);
                    if ($contact->level == 1) {
                        $warninger->warninger_body = $warninger->warninger_body ? $warninger->warninger_body.','.$contact->phone : $contact->phone;
                    } else {
                        $warninger->warninger_body_level{$contact->level} = $warninger->warninger_body_level{$contact->level} ? $warninger->warninger_body_level{$contact->level}.','.$contact->phone : $contact->phone;

                    }
                    $warninger->save();
                }
                \Log::info($create);
                break;
            case 'update':
                $find = Contact::find($contact->contact_id);
                $replace = $find->phone;
                $contact->bak=$find->toArray();
                $contact->save();
                $find->update([
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'note' => '冷链变更单修改']);
                $warningers = Warninger::query()->whereRaw('locate('.$replace.',warninger_body) or locate('.$replace.',warninger_body_level2) or locate('.$replace.',warninger_body_level3)')->where('company_id', $apply->company->id)->get();
                foreach ($warningers as $warninger) {
                    $warninger->warninger_body = str_replace($replace, $contact->phone, $warninger->warninger_body);
                    $warninger->warninger_body_level2 = str_replace($replace, $contact->phone, $warninger->warninger_body_level2);
                    $warninger->warninger_body_level3 = str_replace($replace, $contact->phone, $warninger->warninger_body_level3);
                    $warninger->save();
                }

                break;
            case 'delete':
                $find = Contact::find($contact->contact_id);
                $replace = $find->phone;
                $contact->bak=$find->toArray();
                $contact->save();
                $find->delete();
                $warningers = Warninger::query()->whereRaw('locate('.$replace.',warninger_body) or locate('.$replace.',warninger_body_level2) or locate('.$replace.',warninger_body_level3)')->where('company_id', $apply->company->id)->get();
                foreach ($warningers as $warninger) {
                    $warninger_body_arr = explode(',', $warninger->warninger_body);
                    foreach ($warninger_body_arr as $key => $arr) {
                        if ($arr == $replace) {
                            unset($warninger_body_arr[$key]);
                            break;
                        }
                    }
                    $warninger->warninger_body = implode(',', $warninger_body_arr);
                    $warninger_body_level2_arr = explode(',', $warninger->warninger_body_level2);
                    foreach ($warninger_body_level2_arr as $key => $arr2) {
                        if ($arr2 == $replace) {
                            unset($warninger_body_level2_arr[$key]);
                            break;
                        }
                    }
                    $warninger->warninger_body_level2 = implode(',', $warninger_body_level2_arr);
                    $warninger_body_level3_arr = explode(',', $warninger->warninger_body_level3);
                    foreach ($warninger_body_level3_arr as $key => $arr3) {
                        if ($arr3 == $replace) {
                            unset($warninger_body_level3_arr[$key]);
                            break;
                        }
                    }
                    $warninger->warninger_body_level3 = implode(',', $warninger_body_level3_arr);
                    $warninger->save();
                }
                break;
        }
    }
}
