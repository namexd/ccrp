<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\Setting\CoolerAddRequest;
use App\Http\Requests\Api\Ccrp\Setting\CoolerStatusRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerHasVaccineTags;
use App\Models\Ccrp\Product;
use App\Models\Ccrp\Reports\CoolerLog;
use App\Models\Ccrp\Sys\SysCoolerType;
use App\Models\Ccrp\VaccineTags;
use App\Transformers\Ccrp\CoolerHistoryTransformer;
use App\Transformers\Ccrp\CoolerTransformer;
use App\Transformers\Ccrp\CoolerType100Transformer;
use App\Transformers\Ccrp\Sys\CoolerTypeTransformer;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

;

class CoolersController extends Controller
{
    private $cooler;

    public function __construct(Cooler $cooler)
    {

        $this->cooler = $cooler;
    }

    public function index()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids);
        $status = request()->get('status') ?? '1';
        if ($status) {
            $coolers = $coolers->where('status', $status);
        } else {
            $coolers = $coolers->where('status', '<>', 4);
        }
        if (request()->get('has_collector')) {
            $coolers = $coolers->where('collector_num', '>', 0);
        }
        if ($keyword = request()->get('keyword')) {
            $coolers = $coolers->where(function ($query) use ($keyword) {
                $query->where('cooler_name', 'like', '%'.$keyword.'%')->orWhere('cooler_sn', 'like', '%'.$keyword.'%');
            });
        }

        if ($code = request()->get('code')) {
            $coolers = $coolers->where(function ($query) use ($code) {
                $query->where('cooler_id', $code)->orWhere('cooler_sn', $code)->orWhereHas('vaccine_tags', function ($q) use ($code) {
                    $q->where('code', $code)->orWhere('tag_id', $code);
                });
            });

        }
        $vaccine_tags_cooler = $coolers->pluck('cooler_id');
        $vaccine_tags_count = CoolerHasVaccineTags::whereIn('cooler_id', $vaccine_tags_cooler);
        if ($vaccine_tag = VaccineTags::query()->where('code', $code)->orWhere('id', $code)->first()) {
            $vaccine_tags_count = $vaccine_tags_count->where('tag_id', $vaccine_tag->id);
        }
        $vaccine_tags_count = $vaccine_tags_count->count();
        $coolers = $coolers->with('company')
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        $resp = $this->response->paginator($coolers, new CoolerTransformer());
        if (request()->get('count') == 1) {
            $count = [
                'cooler_lk_count' => $this->cooler->getCoolerCountByCoolerType($this->company_ids, [Cooler::设备类型_冷藏冷库, Cooler::设备类型_冷冻冷库], $status),
                'cooler_bx_count' => $this->cooler->getCoolerCountByCoolerType($this->company_ids, [Cooler::设备类型_台式小冰箱, Cooler::设备类型_普通冰箱, Cooler::设备类型_冷藏冰箱, Cooler::设备类型_冷冻冰箱], $status),
            ];
            $resp = $resp->addMeta('count', $count)
                ->addMeta('vaccine_tags_count', $vaccine_tags_count);

        }
        return $resp;
    }

    public function all()
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('status', 1)->with('company')
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->get();
        return $this->response->collection($coolers, new CoolerTransformer());
    }

    public function show($cooler)
    {
        $this->check();
        $cooler = $this->cooler->where(function ($query) use ($cooler) {
            $query->where('cooler_id', $cooler)->orWhere('cooler_sn', $cooler);
        })->whereIn('company_id', $this->company_ids)->first();
        if ($cooler) {
            return $this->response->item($cooler, new CoolerTransformer());
        } else {
            return $this->response->errorMethodNotAllowed('冰箱不存在');
        }
    }

    public function history($cooler)
    {
        $this->check();
        $start = request()->start ?? date('Y-m-d H:i:s', time() - 4 * 3600);
        $end = request()->end ?? date('Y-m-d 23:59:59', strtotime($start));
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        $cooler = $this->cooler->with('collectors')->find($cooler);
        if ($cooler) {
            $data = $cooler->history($start_time, $end_time);
            return $this->response->item($data, new CoolerHistoryTransformer());
        } else {
            return $this->response->noContent();
        }
    }

    public function gspHistory($cooler_id, Request $request)
    {
        $this->check();
        $todayTime = Carbon::now()->startOfDay()->timestamp;
        $start = $request->start ? strtotime($request->start) : $todayTime;
        $end = $request->end ? strtotime($request->end) : ($todayTime + 3600 * 24 - 1);
        $collector_ids = $request->get('collector_ids', null);
        $spacing_time = $request->get('spacing_time', 0);

        if (is_string($collector_ids)) {
            $collector_ids = json_decode($collector_ids, true);
        } else {
            $collector_ids = null;
        }
        $model = $this->cooler;
        $cooler = $model->find($cooler_id);
        if ($cooler['install_time'] > $start)
            $start = $cooler['install_time'];

        if($spacing_time>0)
        {
            $gspHistory = $model->spacingHistory($cooler,$start,$end,$collector_ids,$spacing_time);
        }else{
            $gspHistory = $model->gspHistory($cooler,$start,$end,$collector_ids);
        }
        return $this->response->array(['data' => $gspHistory]);
    }

    public function getCoolerByType($type)
    {
        $this->check();
        $coolers = $this->cooler->whereIn('company_id', $this->company_ids)->where('cooler_type', $type);
        if ($keyword = request()->get('keyword')) {
            $coolers = $coolers->where('cooler_sn', 'like', '%'.$keyword.'%')->orWhere('cooler_name', 'like', '%'.$keyword.'%');
        }
        if ($category_id = request()->get('category_id')) {
            $coolers = $coolers->where('category_id', $category_id);
        }
        $coolers = $coolers->with(['category', 'collectors'])
            ->orderBy('company_id', 'asc')->orderBy('cooler_name', 'asc')->paginate($this->pagesize);
        return $this->response->paginator($coolers, new CoolerType100Transformer());
    }

    public function store(CoolerAddRequest $request)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $request['company_id'] = $this->company->id;
        $request['install_uid'] = $this->user->id;
        $result = $this->cooler->addCooler($request->all());
        return $this->response->item($result, new CoolerTransformer)->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->check();

        $cooler = $this->cooler->find($id);
        $this->authorize('unit_operate', $cooler->company);
        $cooler->fill($request->all());
        $result = $cooler->save();
        if ($result) {
            $this->cooler->flush_collector_num($id);
            if ($cooler['collector_num'] > 0) {
                foreach ($cooler->collectors as $vo) {
                    $vo->warningSetting()->update(['category_id' => $request->category_id]);
                }
            }
        }
        return $this->response->item($cooler, new CoolerTransformer());
    }

    //备用、维修、启用 关闭探头，关闭报警
    public function coolerStatus(CoolerStatusRequest $request, $id)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $cooler = $this->cooler->find($id);
        $status = $request->status;
        $this->cooler->ChangeCoolerStatus($cooler, $status, $request->get('note',''), $this->user->id);
        return $this->response->item($cooler, new CoolerType100Transformer());

    }

    public function gspWarningOff($id)
    {
        $this->check();
        $this->authorize('unit_operate', $this->company);
        $cooler = $this->cooler->findOrFail($id);
        $cooler->setWarningByStatus(0);
        return $this->response->item($cooler, new CoolerType100Transformer());
    }

    public function coolerType()
    {
        return $this->response->collection(SysCoolerType::all(), new CoolerTypeTransformer());
    }

    public function getCoolerStatus()
    {
        $result = [];
        foreach (Cooler::$status as $key => $status) {
            $result[] = [
                'value' => $key,
                'label' => $status,
            ];
        }
        return $this->response->array($result);
    }

    public function addVaccineTags($id)
    {
        $cooler = $this->cooler->find($id);
        $tags = request()->get('tags');
//        if ($cooler->company->hasUseSettings(Company::单位设置_可以添加仓位, 1)) {
        $cooler->vaccine_tags()->sync($tags);
        return $this->response->noContent();
//        } else {
//            return $this->response->errorMethodNotAllowed('该单位没有权限');
//        }
    }
}
