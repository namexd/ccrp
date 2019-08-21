<?php

namespace App\Models;

use App\Models\Ccrp\Company;
use function app\Utils\dateFormatByType;
use Illuminate\Database\Eloquent\Model;

class CheckTask extends Model
{

    protected $fillable = [
        'company_id', 'template_id', 'start','end', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function template()
    {
        return $this->belongsTo(CheckTemplate::class, 'template_id');
    }

    public function buildTask()
    {
        $companies = Company::whereHas('tags', function ($query) {
            $query->where('slug', 'vip');
        })->select('id')->get();
        $templates = CheckTemplate::where('status', 1)->get();
        if (!$templates->isEmpty()) {

            foreach ($companies as $k => $company) {
                foreach ($templates as $key => $template) {
                    $date=dateFormatByType($template->cycle_type,$template->start.'-'.$template->end);
                    $attributes = ['company_id' => $company->id, 'template_id' => $template->id,'start'=>$date['start'],'end'=>$date['end']];
                    $check_task = CheckTask::updateOrCreate($attributes, $attributes);
                    if ($check_task) {
                        $variables = CheckTemplateVariable::pluck('key');
                        if (!$variables->isEmpty()) {
                            foreach ($variables as $item) {
                                $add_result = ['task_id' => $check_task->id, 'key' => $item];
                                CheckTaskResult::updateOrCreate($add_result, $add_result);
                            }
                        } else {
                            die('没有定义模板变量');
                        }

                    }
                }
            }
          return   $this->runTask();
        } else {
            die('没有任务');
        }

    }

    public function runTask()
    {
        $a=new CheckTaskResult;
        while ($result = CheckTaskResult::whereNull('value')->orWhere('status',0)->first()) {
            $var = CheckTemplateVariable::where('key', $result->key)->first();
            $model = 'App\\Models\\' . $var->module;
            $object = new  $model;
            $object=$object->setConnection('dbyingyongread');
            $function = $var->function;
            $result->value = json_encode($object->$function($result->task->company_id, $result->task->template->cycle_type));
            $result->status=1;
            $result->save();
            $total = CheckTaskResult::where('task_id', $result->task_id)->count();
            $notnull = CheckTaskResult::where('task_id', $result->task_id)->whereNotNull('value')->where('status',1)->count();
            if ($total == $notnull) {
                $task = CheckTask::find($result->task_id);
                $task->status = 1;
                $task->save();
            }
        }
        return true;
    }
}
