<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Models\Ccrp\Cooler;
use App\Models\Ccrp\CoolerDetail;
use App\Models\Ccrp\Sys\SysCoolerDetail;
use App\Transformers\Ccrp\Sys\CoolerDetailTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

class CoolerDetailsController extends Controller
{
    public $model;

    public function __construct(CoolerDetail $coolerDetail)
    {
        $this->model = $coolerDetail;
    }

    public function index(Request $request)
    {
        $this->check();
        $cooler_ids = Cooler::whereIn('company_id', $this->company_ids)->pluck('cooler_id');
        $details = $this->model->whereIn('cooler_id', $cooler_ids)->get();
        $fractal = new Manager();
        $sys_details = new Collection(SysCoolerDetail::all(), new CoolerDetailTransformer());
        $array = $fractal->createData($sys_details)->toArray();
        return $this->response->collection($details, new \App\Transformers\Ccrp\CoolerDetailTransformer())
            ->addMeta('sys_details', $array);
    }

    public function store()
    {
        $this->check();
        $result = [];
        $cooler_id = request()->get('cooler_id') ?? 0;
        $cooler_sn = request()->get('cooler_sn') ?? 0;
        if (!($cooler_id || $cooler_sn)) {
            return $this->response->errorMethodNotAllowed('cooler_id或者cooler_sn不能为空');
        }
        if ($cooler_sn) {
            $cooler = Cooler::where('cooler_sn', $cooler_sn)->first();
            if (!$cooler) {
                return $this->response->errorMethodNotAllowed('冰箱不存在');
            }
            $cooler_id = $cooler->cooler_id;
        }
        $details = request()->get('details');
        $details = is_array($details) ? $details : json_decode($details, true);
        foreach ($details as $detail) {
            $value = $detail['value'];
            $search = [
                'cooler_id' => $cooler_id,
                'sys_id' => $detail['sys_id'],
            ];
            if ($detail['sys_id'] == 19) {
                $value = ($this->userinfo)['realname'];
            }
            if ($detail['sys_id'] == 20) {
                $value = Carbon::now()->toDateTimeString();
            }
            $attribute = ['value' => $value];
            $result[] = $this->model->updateOrCreate($search, $attribute);
        }
        return $this->response->array($result);
    }

    public function show($id)
    {
        $cooler = Cooler::where('cooler_id', $id)->orWhere('cooler_sn', $id)->first();
        if (!$cooler) {
            return $this->response->errorMethodNotAllowed('无法识别冰箱信息');
        }
        $details = $this->model->where('cooler_id', $cooler->cooler_id)->get();
        $fractal = new Manager();
        $sys_details = new Collection(SysCoolerDetail::whereRaw(' (locate('.$cooler->cooler_type.',note) or length(note)=0 or ISNULL(note))')->orderBy('sort')->get(), function ($coolerDetail) use ($cooler) {
            $current_value = $this->getCurrentValue($coolerDetail, $cooler);
            $arr = [
                'id' => $coolerDetail->id,
                'name' => $coolerDetail->name,
                'category' => $coolerDetail->category,
                'slug' => $coolerDetail->slug,
                'description' => $coolerDetail->description,
                'note' => $coolerDetail->note,
                'sys_value' => $coolerDetail->value,
                'current_value' => $current_value,
                'created_at' => Carbon::parse($coolerDetail->created_at)->toDateTimeString(),
                'updated_at' => Carbon::parse($coolerDetail->updated_at)->toDateTimeString(),
            ];
            return $arr;
        });
        $arrays = $fractal->createData($sys_details)->toArray();
        $new_array = [];
        foreach ($arrays['data'] as $key => $array) {
            if (strlen($array['note']) == 0 || in_array($cooler->cooler_type, explode(',', $array['note']))) {
                $new_array['data'][] = $array;
            }
        }
        return $this->response->collection($details, new \App\Transformers\Ccrp\CoolerDetailTransformer())
            ->addMeta('sys_details', $new_array);
    }

    public function getCurrentValue($coolerDetail, $cooler)
    {
        switch ($coolerDetail->slug) {
            case 'cooler_group':
                return Cooler::getTypeGroup($cooler->cooler_type);
                break;
            case 'brand':
                return $cooler->cooler_brand;
                break;
            case 'model':
                return $cooler->cooler_model;
                break;
            case 'nipis_code':
                return $cooler->cooler_cdc_sn;
                break;
            case 'cooler_type':
                return $cooler['cooler_type'] ? Cooler::COOLER_TYPE[$cooler['cooler_type']] : '未知';
                break;
            case 'is_medical':
                return $cooler['is_medical']?Cooler::IS_MEDICAL[$cooler['is_medical']]:'未知';
                break;
            default :
                return $cooler->{$coolerDetail->slug} ?? '';
                break;
        }
    }


}
