<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\EquipmentChangeApply;
use App\Transformers\EquipmentChangeApplyTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EquipmentChangeApplyController extends Controller
{
    protected $model;

    public function __construct(EquipmentChangeApply $equipmentChangeApply)
    {
        parent::__construct();
        $this->model = $equipmentChangeApply;
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
        $company_ids=$this->company_ids;
        if($request->has('status'))
        {
            $this->model=$this->model->where('status',$request->status);
        }
        $data = $this->model->with(['company','details', 'news'])->whereIn('company_id',$company_ids)->orderBy('id','desc')->paginate($request->pagesize??$this->pagesize);
        return $this->response->paginator($data, new EquipmentChangeApplyTransformer());
    }

    public function getChangeType()
    {
        $this->check();
        return $this->response->array(['data' => $this->model->getChangeType()]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check();
        $result = $this->model->add($request->all());
        if ($result instanceof Model)
            return $this->response->item($result,new EquipmentChangeApplyTransformer())->statusCode(201);
        else
            $this->response->errorInternal($result);
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
        return $this->response->item($equipment_change_apply, new EquipmentChangeApplyTransformer());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
