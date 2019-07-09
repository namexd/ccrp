<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Contact;
use App\Models\Ccrp\Warninger;
use App\Transformers\Ccrp\ContactTransformer;

class ConcatsController extends Controller
{
    public function index()
    {
        $this->check();
        $concats = Contact::whereIn('company_id',$this->company_ids)->where('status',1)->with('company')
            ->orderBy('company_id','asc')->paginate(request()->get('pagesize')??$this->pagesize);

        return $this->response->paginator($concats, new ContactTransformer());
    }

    public function hasPhone($company_id,$phone)
    {
        $concat = Contact::where('company_id',$company_id)->where('status',1)->where('phone',$phone)->first();
        return $concat?$this->response->item($concat, new ContactTransformer()):$this->response->noContent();

    }

    public function store()
    {
        $this->check();
        $request=request()->all();
        $request['create_uid']=$this->user->id;
        $request['company_id']=$this->company->id;
        $request['create_time']=time();
        $concat=Contact::create($request);
        return $this->response->item($concat, new ContactTransformer())->setStatusCode(201);
    }

    public function update($id)
    {
        $this->check();
        $request=request()->all();
        $concat=Contact::find($id);
        $request['create_uid']=$this->user->id;
        $request['company_id']=$this->company->id;
        $request['create_time']=time();
        $concat->fill($request);
        $concat->save();
        return $this->response->item($concat, new ContactTransformer());
    }

    public function destroy($id)
    {
        $concat=Contact::find($id);
        if (!$concat)
        {
            return $this->response->errorBadRequest('该联系人不存在');
        }
        $phone = $concat['phone'];
        if($phone !='')
        {
            $map['company_id'] = $concat['company_id'];
            $warninger =Warninger::where($map)->whereRaw("FIND_IN_SET(" . $phone . ",warninger_body) or FIND_IN_SET(" . $phone . ",warninger_body_level2) or FIND_IN_SET(" . $phone . ",warninger_body_level3)  ")->get()->toArray();
            if ($warninger) {
                return $this->response->errorInternal('删除失败,该联系人用于' . count($warninger) . '个报警通道:' . $warninger[0]['warninger_name'].'');
            }
        }
        $concat->delete();
        return $this->response->noContent();
    }

}
