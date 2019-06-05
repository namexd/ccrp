<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\TablesSyncCollectorRequest;
use App\Models\Ccrp\TablesSync;
use function App\Utils\array_trim;
use Illuminate\Http\Request;

class TablesSyncsController extends Controller
{

    public function index(TablesSyncCollectorRequest $request)
    {
        return $this->syncs($request);
    }

    public function syncs(TablesSyncCollectorRequest $request)
    {
        $this->check($this->user());
        $time = $request->time >0 ?$request->time : -1;
        $table_sync = TablesSync::where('status', '1');
        if($request->table)
        {
            $table_sync->where('table_name',$request->table);
        }
        $tables = $table_sync->get();
        $result = [];
        foreach($tables as $table)
        {
            $table_sync_fileds = array_trim(explode(',', $table->sync_fields));
            $table_timestamp_fields = array_trim(explode(',', $table->timestamp_fields));
            $model_name = 'App\\Models\\'.$table->model_name ;
            $model = (new  $model_name )->whereIn($table->company_id_field, $this->company_ids);
            $model->where(function ($query) use ($table_timestamp_fields, $time) {
                foreach ($table_timestamp_fields as $vo) {
                    $query->orWhere($vo, '>', $time);
                }
            });

//            $builder =$model->limit(100)->select($table_sync_fileds);
//            $bindings = $builder->getBindings();
//            $sql = str_replace('?', '%s', $builder->toSql());
//            $sql = sprintf($sql, ...$bindings);
//            dd($sql);

            $result[$table->table_name] = $model->limit(250)->select($table_sync_fileds)->get();

        }
        return $this->response->created(null,$result);
//        return $this->response->collection($collectors, new TablesSyncsCollectorTransformer());
    }
}
