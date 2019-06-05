<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Certification;
use App\Transformers\Ccrp\CertificationTransformer;
use Illuminate\Http\Request;

class CertificationsController extends Controller
{
    protected $model;

    public function __construct(Certification $certification)
    {
        parent::__construct();
        $this->model = $certification;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->check();
        if ($request->has('company_id')&&$company=$request->company_id)
        {
            $this->model=$this->model->where('company_id',$company);
        }
        $data = $this->model->orderBy('id','desc')->paginate($request->pagesize??$this->pagesize);
        return $this->response->paginator($data, new CertificationTransformer());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->check();
        $equipment_change_apply = $this->model->findOrFail($id);
        return $this->response->item($equipment_change_apply, new CertificationTransformer());
    }

}
