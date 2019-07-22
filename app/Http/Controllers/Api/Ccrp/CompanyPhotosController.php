<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\CompanyPhoto;
use App\Models\Ccrp\Sys\SysCompanyPhoto;
use App\Transformers\Ccrp\CompanyPhotoTransformer;
use App\Transformers\Ccrp\Sys\SysCompanyPhotoTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

class CompanyPhotosController extends Controller
{
    public $model;

    public function __construct(CompanyPhoto $companyPhoto)
    {
        $this->model = $companyPhoto;
    }

    public function index(Request $request)
    {
        $this->check();
        $photos=$this->model->where('company_id',$this->company->id)->paginate($request->pagesize??$this->pagesize);
        $fractal = new Manager();
        $sys_photos=new Collection(SysCompanyPhoto::all(),new SysCompanyPhotoTransformer());
        $array = $fractal->createData($sys_photos)->toArray();
        return $this->response->paginator($photos,new CompanyPhotoTransformer())
            ->addMeta('sys_photos',$array);
    }

}
