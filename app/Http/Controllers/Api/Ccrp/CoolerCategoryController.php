<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CoolerCategoryRequest;
use App\Models\CoolerCategory;
use App\Transformers\Ccrp\CoolerCategoryTransformer;
use Illuminate\Http\Request;


class CoolerCategoryController extends Controller
{
    private $coolerCategory;

    public function __construct(CoolerCategory $coolerCategory)
    {

        $this->coolerCategory = $coolerCategory;
    }

    public function index()
    {
        $this->check();
        $company_id=$this->company->id;
        $categories=$this->coolerCategory->where('company_id',$company_id)->get();
        return $this->response->collection($categories,new CoolerCategoryTransformer());
    }

    public function show($id)
    {
        $this->check();
        $categories = $this->coolerCategory->find($id);
        if ($categories) {
            return $this->response->item($categories, new CoolerCategoryTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function store(CoolerCategoryRequest $request)
    {

        $this->check();
        $attributes=$request->all();
        $attributes['company_id']=$this->company->id;
        $attributes['status']=1;
        $result = $this->coolerCategory->create($attributes);
        return $this->response->item($result, new CoolerCategoryTransformer)->setStatusCode(201);
    }

    public function update(CoolerCategoryRequest $request, $id)
    {
        $this->check();
        $category=$this->coolerCategory->find($id);
        $category->fill($request->all());
        $category->save();
        return $this->response->item($category,new CoolerCategoryTransformer());
    }

}
