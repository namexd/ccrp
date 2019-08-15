<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Contact;
use App\Models\Ccrp\EquipmentChangeContact;
use function App\Utils\hidePhone;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class EquipmentContactTransformer extends TransformerAbstract
{
    protected $availableIncludes=['warninger'];
    public function transform(EquipmentChangeContact $contact)
    {
        $rs = [
            'id' => $contact->id,
            'apply_id'=>$contact->apply_id,
            'action'=>$contact->action,
            'contact_id'=>$contact->contact_id,
            'name'=>$contact->name,
            'phone'=>$contact->phone,
            'warninger_id'=>$contact->warninger_id,
            'level'=>$contact->level,
            'bak'=>$contact->bak,
            'created_at'=>$contact->created_at->toDateTimeString()
        ];
        return $rs;
    }

    public function includeWarninger(EquipmentChangeContact $contact)
    {
        if ($contact->warninger)
        return $this->item($contact->warninger,new WarningerTransformer());
        else
            return $this->null();
    }
}
