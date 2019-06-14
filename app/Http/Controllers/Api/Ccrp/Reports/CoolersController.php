<?php

namespace App\Http\Controllers\Api\Ccrp\Reports;

use App\Http\Requests\Api\Ccrp\Report\DateRangeRequest;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\Reports\CoolerLog;
use App\Transformers\Ccrp\Reports\CoolerLogTransformer;
use App\Transformers\Ccrp\Reports\WarningersTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Input;


/**
 * 冷链操作日志表
 * Class CoolersController
 * @package App\Http\Controllers\Api\Ccrp\Reports
 */
class CoolersController extends Controller
{
    /**
     * note：冷链操作日志表
     * author: xiaodi
     * date: 2019/3/26 15:43
     * @param DateRangeRequest $request
     * @param CoolerLog $coolerLog
     * @return Response
     */
    public function logs(DateRangeRequest $request,CoolerLog $coolerLog)
    {
        $this->check($this->company_id);
        $start = strtotime(Input::get('start'));
        $end = strtotime(Input::get('end'));
        $result=$coolerLog->getListByDate($this->company_ids,$start,$end)->paginate($this->pagesize);
        $transformer=new CoolerLogTransformer();
        return $this->response->paginator($result,$transformer)->addMeta('colums',$transformer->columns());
    }

    public function countCoolerNumber(Cooler $cooler)
    {
        $this->check($this->company_id);
        $company_id=$this->company_id??$this->company->id;
        $parent=Company::find($company_id);
        $companies=Company::where('pid', $company_id)->where('status',1)->where('company_group', $parent->company_group)->get();
        $result=[];
        foreach ($companies as $key=>$company)
        {
            $companyIds=$company->ids();
            $result[$key]=$cooler->getCountByType($companyIds,request()->toArray());
            $result[$key]['title']=$company->title;
            $result[$key]['region_code']=$company->region_code;
        }
        $data['data']=$result;
        $data['meta']['columns']['parent']=$parent->title;
        return $this->response->array($data);

    }
    public function countCoolerVolume(Cooler $cooler)
    {
        $this->check($this->company_id);
        $company_id=$this->company_id??$this->company->id;
        $parent=Company::find($company_id);
        $companies=Company::where('pid', $company_id)->where('status',1)->where('company_group', $parent->company_group)->get();
        $result=[];
        foreach ($companies as $key=>$company)
        {
            $companyIds=$company->ids();
            $result[$key]=$cooler->getVolumeByStatus($companyIds,request()->toArray());
            $result[$key]['title']=$company->title;
            $result[$key]['region_code']=$company->region_code;
        }
        $data['data']=$result;
        $data['meta']['columns']['parent']=$parent->title;
        return $this->response->array($data);

    }
    public function countCoolerStatus(Cooler $cooler)
    {
        $this->check($this->company_id);
        $company_id=$this->company_id??$this->company->id;
        $parent=Company::find($company_id);
        $companies=Company::where('pid', $company_id)->where('status',1)->where('company_group', $parent->company_group)->get();
        $result=[];
        foreach ($companies as $key=>$company)
        {
            $companyIds=$company->ids();
            $result[$key]=$cooler->getCoolerStatus($companyIds,request()->toArray());
            $result[$key]['title']=$company->title;
            $result[$key]['region_code']=$company->region_code;
        }
        $data['data']=$result;
        $data['meta']['columns']['parent']=$parent->title;
        return $this->response->array($data);

    }
    public function getCoolerTypes(Cooler $cooler)
    {
        $result=$cooler->getCoolerTypes();
        return $this->response->array($result);
    }
}
