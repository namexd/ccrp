<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    public function checkAll()
    {
        $check = $this;

        $model = 'App\\Models\\' . $check->object;
        $method = $check->object_method;
        $object = new $model;
        $result = $object->$method();
        $count = count($result);
        $log = new CheckLog();
        $log->check_id = $check->id;
        $log->check_result = $count;
        $log->save();
        if ($count > 0) {
            foreach ($result as $item) {
                $check_result = CheckResult::where('object', $check->object)
                        ->where('object_key', $check->object_key)
                        ->where('object_value', $item->object_value)
                        ->first()
                    ?? new CheckResult();
                $check_result->check_id = $check->id;
                $check_result->result = $item->result;
                $check_result->object = $check->object;
                $check_result->object_key = $check->object_key;
                $check_result->object_value = $item->object_value;
                $check_result->check_times = ($check_result->check_times ?? 0) + 1;
                $check_result->save();
            }
        }
        $check->exec_times = $check->exec_times + 1;
        $check->last_exec_time = Carbon::now();
        $check->save();
        return $count;
    }

    public function checkObject($object_value)
    {
        $check = $this;

        $model = 'App\\Models\\' . $check->object;
        $method = $check->object_method;
        $object = new $model;
        $item = $object->$method($object_value);
        $check_result = CheckResult::where('object', $check->object)
            ->where('object_key', $check->object_key)
            ->where('object_value', $item->object_value)
            ->first();
        $check_result->check_id = $check->id;
        $check_result->result = $item->result;
        if($item->result ==0)
        {
            $check_result->status = 1;
        }
        $check_result->check_times = ($check_result->check_times ?? 0) + 1;
        $check_result->save();
        return  $item->result;
    }
}
