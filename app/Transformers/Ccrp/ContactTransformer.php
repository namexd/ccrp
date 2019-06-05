<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Contact;
use function App\Utils\hidePhone;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    public function transform(Contact $contact)
    {
        $rs = [
            'id' => $contact->contact_id,
            'name' => $contact->name,
            'phone' => hidePhone($contact->phone),
            'job' => $contact->job,
            'note' => $contact->note,
            'company_id' => $contact->company_id,
            'company' => $contact->company->title,
            'created_at' => $contact->create_time>0?Carbon::createFromTimestamp($contact->create_time)->toDateTimeString():0,
        ];
        if(request()->get('with'))
        {
            unset($rs['company_id']);
        }
        return $rs;
    }
}