<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\CompanyPhoto;
use App\Models\Ccrp\Sys\SysCompanyPhoto;
use App\Transformers\Ccrp\CompanyListTransformer;
use App\Transformers\Ccrp\CompanyPhotoTransformer;
use App\Transformers\Ccrp\Sys\SysCompanyPhotoTransformer;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

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
        $company=new Item($this->company,new CompanyListTransformer());
        $array2= $fractal->createData($company)->toArray();
        return $this->response->paginator($photos,new CompanyPhotoTransformer())
            ->addMeta('sys_photos',$array)
            ->addMeta('company',$array2);

    }

    public function store()
    {
        $this->check();
        $result=[];
        $company_id=$this->company->id;
        $photos=request()->get('photos');
        $photos=is_array($photos)?$photos:json_decode($photos,true);
        foreach ($photos as $photo)
        {
            $search=[
                'company_id'=>$company_id,
                'sys_id'=>$photo['sys_id'],
            ];
            $attribute=['value'=>$photo['value']];
            $result[]=$this->model->updateOrCreate($search,$attribute);
        }
        return $this->response->array($result);
    }

}
