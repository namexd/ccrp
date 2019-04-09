<?php

namespace App\Transformers;

use App\Models\Contact;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    public function transform(Contact $contact)
    {
        return [
            'id' => $contact->contact_id,
            'name' => $contact->name,
            'phone' => $contact->phone,
            'job' => $contact->job,
            'note' => $contact->note,
            'company_id' => $contact->company_id,
            'company' => $contact->company->title,
            'created_at' => $contact->create_time>0?Carbon::createFromTimestamp($contact->create_time)->toDateTimeString():0,
        ];
    }
}
