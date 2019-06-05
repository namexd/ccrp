<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Contact;
use App\Transformers\Ccrp\ContactTransformer;

class ConcatsController extends Controller
{
    public function index()
    {
        $this->check();
        $concats = Contact::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc')->paginate($this->pagesize);

        return $this->response->paginator($concats, new ContactTransformer());
    }

    public function hasPhone($company_id,$phone)
    {
        $concat = Contact::where('company_id',$company_id)->where('status',1)->where('phone',$phone)->first();
        return $concat?$this->response->item($concat, new ContactTransformer()):$this->response->noContent();

    }

}
