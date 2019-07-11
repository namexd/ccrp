<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Contact;
use App\Models\Ccrp\Warninger;
use function App\Utils\hidePhone;
use Carbon\Carbon;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class WarningerTransformer extends TransformerAbstract
{
    protected $availableIncludes=['contacts'];
    public function transform(Warninger $warninger)
    {
        $rs =  [
            'id' => $warninger->warninger_id,
            'warninger_name' => $warninger->warninger_name,
            'warninger_type' => $warninger->warninger_type,
//            'warninger_body_pluswx' => $warninger->warninger_body_pluswx?$this->formatPluswx($warninger->warninger_body_pluswx):'',
//            'warninger_body_level2_pluswx' => $warninger->warninger_body_level2_pluswx?$this->formatPluswx($warninger->warninger_body_level2_pluswx):'',
//            'warninger_body_level3_pluswx' => $warninger->warninger_body_level3_pluswx?$this->formatPluswx($warninger->warninger_body_level3_pluswx):'',
            'warninger_type_level2' => $warninger->warninger_type_level2,
            'warninger_type_level3' => $warninger->warninger_type_level3,
            'warninger_type_name' => $warninger->warninger_type_name,
            'warninger_type_level2_name' => $warninger->warninger_type_level2_name,
            'warninger_type_level3_name' => $warninger->warninger_type_level3_name,
            'warninger_body' => $warninger->warninger_body,
            'warninger_body_level2' => $warninger->warninger_body_level2,
            'warninger_body_level3' =>  $warninger->warninger_body_level3,
            'bind_times' => $warninger->bind_times,
            'created_at' =>$warninger->ctime?Carbon::createFromTimestamp($warninger->ctime)->toDateTimeString():'',
        ];
        if( in_array($rs['warninger_type'],['短信','电话']))
        {
            $contacts = Contact::where('company_id',$warninger->company_id)->pluck('name','phone');
            $rs['warninger_body'] = $this->formatPhone($warninger->warninger_body,$contacts);
            $rs['warninger_body_level2'] = $this->formatPhone($warninger->warninger_body_level2,$contacts);
            $rs['warninger_body_level3'] = $this->formatPhone($warninger->warninger_body_level3,$contacts);
        }
        if(request()->get('with'))
        {
            $rs['meta'] = ['header' => $warninger->warninger_name];
        }
        if(strpos(request()->get('with'),'contacts')!==false)
        {
            $rs['meta']['contacts'] = $this->getContacts($warninger);
        }
        return $rs;
    }

    private function formatPhone($phones_str,$contacts)
    {
        if($phones_str=="")return "";
        $rs = "";
        if(strpos($phones_str,','))
        {
            $phones = explode(',',$phones_str);
            foreach($phones as $phone)
            {
                $rs .= isset($contacts[$phone])? $contacts[$phone]."(".hidePhone($phone)."),":hidePhone($phone) .',';
            }
        }else{
            $rs = isset($contacts[$phones_str])? $contacts[$phones_str]."(".hidePhone($phones_str).")":hidePhone($phones_str);
        }
        return $rs;
    }
    public function formatPluswx($id_Str)
    {
        if($id_Str=="")return "";
        $rs = "";
        if(strpos($id_Str,','))
        {
            $ids = explode(',',$id_Str);
            foreach($ids as $id)
            {
                $contact=Contact::find($id);
                $rs .= $contact['name']."(".hidePhone($contact['phone'])."),";
            }
        }else{

            $contact=Contact::find($id_Str);
            $rs= $contact['name']."(".hidePhone($contact['phone'])."),";
        }
        return $rs;
    }

    public function getContacts(Warninger $warninger)
    {
        $data=[];
        if ($warninger->warninger_body) {
            $phones1 = explode(',', $warninger->warninger_body);
            $contacts1=$warninger->contacts($phones1);
            $data['warninger_body']=$contacts1;
        }
        if ($warninger->warninger_body_level2) {
            $phones2 = explode(',', $warninger->warninger_body_level2);
            $contacts2=$warninger->contacts($phones2);
            $data['warninger_body_level2']=$contacts2;
        }
        if ($warninger->warninger_body_level3) {
            $phones3= explode(',', $warninger->warninger_body_level3);
            $contacts3=$warninger->contacts($phones3);
            $data['warninger_body_level3']=$contacts3;
        }
        return $data;
    }
}
