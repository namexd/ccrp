<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\VehicleMapRequest;
use App\Models\Ccrp\Collector;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\DataHistory;
use App\Models\Ccrp\Printer;
use App\Models\Ccrp\Vehicle;
use App\Traits\ControllerDataRange;
use App\Transformers\Ccrp\CollectorDetailTransformer;
use App\Transformers\Ccrp\VehicleTransformer;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;

class VehiclesController extends Controller
{
    private $vehicle;

    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function index()
    {
        $this->check();
        $vehicles = $this->vehicle->whereIn('company_id', $this->company_ids)->where('status', 1)
            ->paginate($this->pagesize);
        $transform= new VehicleTransformer();
        return $this->response->paginator($vehicles,$transform)->addMeta('columns',$transform->columns());
    }


    public function refresh($vehicle_id)
    {
        $this->check();
        $vehicle=$this->vehicle->find($vehicle_id);
        $new_vehicle=$vehicle->refresh_address();
        $transform= new VehicleTransformer();
        return $this->response->paginator($new_vehicle,$transform)->addMeta('columns',$transform->columns());

    }
    public function current($vehicle_id)
    {
        $this->check();
        $vehicle=$this->vehicle->find($vehicle_id);
        $data['data']['url']=$this->vehicle::VECHICLE_CONFIG['VEHICLE_ORIENTATION'].'?vehicle='.$vehicle['vehicle'];
        return $this->response->array($data);
    }


    public function vehicle_temp(Request $request)
    {
        $this->check();
        if (!($request->has('id')||$request->has('vehicle')))
        {
            return $this->response->error('请输入车牌号或者车辆id',422);
        }
        if ($request->has('start') and $request->has('end')) {
            $start = $request->start;
            $end = $request->end;
        } else {
            $start = date('Y-m-d H:i:s', time() - 3600);
            $end = date('Y-m-d H:i:s', time());
        }
        $lists=$this->vehicle->vehicle_temp($request->all(),$start,$end);
        if ($lists)
        {
            $resource = new Collection($lists, function(array $list) {
                return [
                    'reg_name'=>$list['RegName'],
                    'rcv_dt'=>Carbon::parse($list['RcvDT'])->toDateTimeString(),
                    'temperature'=>$list['Temperature'],
                    'temperature2'=>$list['Temperature2'],
                    'temperature3'=>$list['Temperature3'],
                    'temperature4'=>$list['Temperature4'],

                ];
            });
            $fractal = new Manager();
            return $this->response->array($fractal->createData($resource)->toArray());
        }
       return $this->response->errorInternal('没有数据');
    }

    public function vehicle_map(VehicleMapRequest $request)
    {
        $vehicle = $request->vehicle;
        $start =$request->start;
        $end = $request->end;
        return $this->response->array([
            'data'=>
                [
                    'url'=>$this->vehicle::VECHICLE_CONFIG['VEHICLE_PlAYTRACk'].'?vehicle=' . $vehicle . '&btime=' . $start . '&etime=' . $end
                ]
        ]);

    }
}
