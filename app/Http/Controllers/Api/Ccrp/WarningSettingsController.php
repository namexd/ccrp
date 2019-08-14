<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\WarningEventRequest;
use App\Http\Requests\Api\Ccrp\WarningSettingRequest;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Sys\Setting;
use App\Models\Ccrp\Warninger;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
use App\Models\Ccrp\WarningSetting;
use App\Transformers\Ccrp\WarningAllEventTransformer;
use App\Transformers\Ccrp\WarningEventTransformer;
use App\Transformers\Ccrp\WarningSenderEventTransformer;
use App\Transformers\Ccrp\WarningSettingTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

class WarningSettingsController extends Controller
{
    private $model;

    public function __construct(WarningSetting $warningSetting)
    {
        $this->model = $warningSetting;
    }

    public function index()
    {
        $this->check();
        $warnings=$this->model->whereIn('company_id',$this->company_ids)->where('status',1);
        if ($keyword=request()->get('keyword'))
        {
            $warnings=$warnings->whereHas('collector',function ($query) use ($keyword){
               $query->where('collector_name','like','%'.$keyword.'%')->whereOr('supplier_collector_id','like','%'.$keyword.'%');
            });
        }
        $warnings=$warnings->orderBy('id','desc')->paginate(request()->get('pagesize')??$this->pagesize);
        return $this->response->paginator($warnings,new WarningSettingTransformer());
    }

    public function show($id)
    {
        $this->check();
        $warning=$this->model->find($id);
        return $this->response->item($warning,new WarningSettingTransformer());
    }

    public function update($id)
    {
        $this->check();
        $request=request()->all();
        if ($request['temp_warning'])
        {
            if (array_has($request,'temp_low') && array_has($request,'temp_high'))
            {
                if ($request['temp_low'] > $request['temp_high']) {

                    $request['temp_low'] = $request['temp_low'] + $request['temp_high'];
                    $request['temp_high'] = $request['temp_low'] - $request['temp_high'];
                    $request['temp_low'] = $request['temp_low'] - $request['temp_high'];

                }
            }
        }
        if ($request['humi_warning'])
        {
            if (array_has($request,'humi_warning_last') && array_has($request,'humi_warning2_last') && array_has($request,'humi_warning3_last')) {
                $request['humi_warning_last'] = $request['temp_warning_last'];
                $request['humi_warning2_last'] = $request['temp_warning2_last'];
                $request['humi_warning3_last'] = $request['temp_warning3_last'];
            }
        }
        $request['set_time'] = time();
        $warning_setting=$this->model->find($id);
        $result= $warning_setting->update($request);

        if ($request['temp_warning'] == 1) {
            //开启离线报警
            $warning_setting->collector()->update(['offline_check', 1]);
        } else {
            //关闭离线报警
            $warning_setting->collector()->update(['offline_check', 0]);

        }
        if ($result)
        {
         return $this->response->item($warning_setting,new WarningSettingTransformer());
        }else
        {
            return $this->response->errorInternal('修改失败');

        }

    }

    public function store(WarningSettingRequest $request)
    {
        $this->check();
        if ($this->model->where('collector_id',$request['collector_id'])->first())
        {
            return $this->response->errorBadRequest('该探头已设置过报警');
        }
        if ($request['temp_warning'])
        {
            if ($request['temp_low'] > $request['temp_high']) {

                $request['temp_low'] = $request['temp_low'] + $request['temp_high'];
                $request['temp_high'] = $request['temp_low'] - $request['temp_high'];
                $request['temp_low'] = $request['temp_low'] - $request['temp_high'];

            }
        }
        if ($request['humi_warning'])
        {
            $request['humi_warning_last'] = $request['temp_warning_last']??$this->model::WARNING_TIME['WARNING_TIME_LAST']['1'];
            $request['humi_warning2_last'] = $request['temp_warning2_last']??$this->model::WARNING_TIME['WARNING_TIME_LAST']['2'];
            $request['humi_warning3_last'] = $request['temp_warning3_last']??$this->model::WARNING_TIME['WARNING_TIME_LAST']['3'];
        }
        $request['set_time'] = time();
        $request['set_uid'] = $this->user->id;
        $request['status'] = 1;
        $request['company_id'] = $this->company->id;

        if ($temp_warning_last=$this->company->hasUseSettings(Company::单位设置_报警延迟时间))
        {
            $temp_warning_last_arr=explode(',',$temp_warning_last->value);
            $request['temp_warning_last']=$temp_warning_last_arr[0];
            $request['temp_warning2_last']=$temp_warning_last_arr[1];
            $request['temp_warning3_last']=$temp_warning_last_arr[2];
        }elseif($temp_warning_last=CompanyHasSetting::query()->whereIn('company_id',$this->company->getParentIds())->first())
        {
            $temp_warning_last_arr=explode(',',$temp_warning_last->value);
            $request['temp_warning_last']=$temp_warning_last_arr[0];
            $request['temp_warning2_last']=$temp_warning_last_arr[1];
            $request['temp_warning3_last']=$temp_warning_last_arr[2];
        }else
        {
            $temp_warning_last=Setting::find(Company::单位设置_报警延迟时间);
            $temp_warning_last_arr=explode(',',$temp_warning_last->value);
            $request['temp_warning_last']=$temp_warning_last_arr[0];
            $request['temp_warning2_last']=$temp_warning_last_arr[1];
            $request['temp_warning3_last']=$temp_warning_last_arr[2];
        }
        $result= $this->model->create($request->all());
        if ($result)
        {
            return $this->response->item($result,new WarningSettingTransformer());
        }else
        {
            return $this->response->errorInternal('添加失败');

        }

    }

}
