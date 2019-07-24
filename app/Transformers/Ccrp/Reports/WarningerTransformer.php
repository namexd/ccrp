<?php

namespace App\Transformers\Ccrp\Reports;

use App\Models\Ccrp\Contact;
use App\Models\Ccrp\Warninger;
use function App\Utils\hidePhone;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class WarningerTransformer extends TransformerAbstract
{
    public function transform(Warninger $setting)
    {
        $rs = [
            'id' => $setting->warninger_id,
            'warninger_name' => $setting->warninger_name,
            'warninger_type' => Warninger::WARNINGER_TYPES[$setting->warninger_type],
            'warninger_type_level2' => $setting->warninger_type_level2 == 0 ? Warninger::WARNINGER_TYPES[$setting->warninger_type] : Warninger::WARNINGER_TYPES[$setting->warninger_type_level2],
            'warninger_type_level3' => $setting->warninger_type_level2 == 0 ? Warninger::WARNINGER_TYPES[$setting->warninger_type] : Warninger::WARNINGER_TYPES[$setting->warninger_type_level3],
            'warninger_body' => $setting->warninger_body,
            'warninger_body_level2' => $setting->warninger_body_level2,
            'warninger_body_level3' => $setting->warninger_body_level3,
            'bind_times' => $setting->bind_times,
            'created_at' => $setting->ctime ? Carbon::createFromTimestamp($setting->ctime)->toDateTimeString() : '',
        ];
        if (in_array($setting->warninger_type, [Warninger::发送类型_短信, Warninger::发送类型_电话])) {
            $contacts = Contact::where('company_id', $setting->company_id)->pluck('name', 'phone');
            $rs['warninger_body'] = $this->formatPhone($setting->warninger_body, $contacts);
            $rs['warninger_body_level2'] = $this->formatPhone($setting->warninger_body_level2, $contacts);
            $rs['warninger_body_level3'] = $this->formatPhone($setting->warninger_body_level3, $contacts);
        }
        if (request()->get('with')) {
            $rs['meta'] = ['header' => $setting->warninger_name];
        }
        return $rs;
    }

    private function formatPhone($phones_str, $contacts)
    {
        if ($phones_str == "") return "";
        $rs = "";
        if (strpos($phones_str, ',')) {
            $phones = explode(',', $phones_str);
            foreach ($phones as $phone) {
                $rs .= isset($contacts[$phone]) ? $contacts[$phone]."(".hidePhone($phone).")," : hidePhone($phone).',';
            }
        } else {
            $rs = isset($contacts[$phones_str]) ? $contacts[$phones_str]."(".hidePhone($phones_str).")" : hidePhone($phones_str);
        }
        return $rs;
    }
}
