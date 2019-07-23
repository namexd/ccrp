<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\VaccineTags;
use App\Transformers\Ccrp\VaccineTagTransformer;
use Illuminate\Http\Request;


class VaccineTagsController extends Controller
{
    private $model;

    public function __construct(VaccineTags $vaccineTags)
    {

        $this->model= $vaccineTags;
    }

    public function getCategory()
    {
        return $this->response->array(['data'=>$this->model->getCategory()]);
    }

    public function index()
    {
        $categories=$this->model;
        if ($name=request()->get('name'))
        {
            $categories=$categories->where('name','like',$name.'%');
        }
        $categories=$categories->paginate(\request()->get('pagesize')??$this->pagesize);
        return $this->response->paginator($categories,new VaccineTagTransformer());
    }

    public function show($id)
    {
        $this->check();
        $vaccineTags = $this->model->find($id);
        if ($vaccineTags) {
            return $this->response->item($vaccineTags, new VaccineTagTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function store(Request $request)
    {

        $this->check();
        $attributes=$request->all();
        $attributes['company_id']=$this->company->id;
        $attributes['status']=1;
        $result = $this->model->create($attributes);
        return $this->response->item($result, new VaccineTagTransformer)->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->check();
        $vaccineTags=$this->model->find($id);
        $vaccineTags->fill($request->all());
        $vaccineTags->save();
        return $this->response->item($vaccineTags,new VaccineTagTransformer());
    }

}
