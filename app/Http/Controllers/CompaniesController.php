<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\CompanyRequest;
use App\Models\Company;
use App\Models\WarningEvent;
use App\Models\WarningSenderEvent;
use App\Transformers\CompanyInfoTransformer;
use App\Transformers\CompanyListTransformer;
use App\Transformers\CompanyTransformer;
use function App\Utils\get_last_months;
use function App\Utils\get_month_first;
use function App\Utils\get_month_last;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use League\Fractal\Resource\Item;

class CompaniesController extends Controller
{

    public function index(CompanyRequest $request, $id = null)
    {
        $this->check($id);
        $companies = Company::whereIn('id', $this->company_ids)->where('status', 1);

        if (isset($request->hidden) and $request->hidden == 'admin') {
            $companies->where('cdc_admin', 0);
        }
        $companies = $companies->orderBy('pid', 'asc')->orderBy('title', 'asc')->get();

        if ($id == null) {
            $current = $this->company;
        } else {
            $ids = (new Company())->getSubCompanyIds($id);
            if (in_array($ids, $ids)) {
                $current = Company::find($id);
            } else {
                $current = $this->company;
            }
        }
        $current_company = [
            'id' => $current->id,
            'title' => $current->title,
            'short' => $current->short,
            'address' => $current->address,
            'address_lat' => $current->address_lat,
            'address_lon' => $current->address_lon,
            'map_level' => $current->map_level,
        ];

        return $this->lwResponse($companies, new CompanyListTransformer(), ['current' => $current_company]);
    }

    public function current($id = null)
    {
        $this->check($id);
//        Cache::forget('companies.'.$this->company->id.'.current');
        $company = Cache::remember('companies.' . $this->company->id . '.current', 10, function () use ($id) {
            return $this->refresh($id);
        });
        return $this->lwResponse($company, new CompanyInfoTransformer());
    }

    private function refresh($id = null)
    {
        $this->check($id);
        $today = strtotime(date('Y-m-d 00:00:00'));
        $company = Company::where('id', $this->company->id)->first();
        $company->alerms_new =
            WarningEvent::whereIn('company_id', $this->company_ids)->where('handled', 0)->count()
            + WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('handled', 0)->count();
        $company->alerms_all =
            WarningEvent::whereIn('company_id', $this->company_ids)->count()
            + WarningSenderEvent::whereIn('company_id', $this->company_ids)->count();
        $company->alerms_today =
            WarningEvent::whereIn('company_id', $this->company_ids)->where('warning_event_time', '>', $today)->count()
            + WarningSenderEvent::whereIn('company_id', $this->company_ids)->where('sensor_event_time', '>', $today)->count();
        $company->save();
        return $company;
    }

    public function tree($id = null)
    {
        $this->check($id);
        //cdc_admin desc,region_code asc,company_group asc, cdc_level asc,pid asc,sort desc,company_type asc,username asc,id asc
        $company = Company::cdcListWithOrders($this->company_ids, $this->company->id, ['id', 'pid', 'title', 'short_title', 'leaves_count']);
        $company_array = $company->toArray();
        $company_top = Company::where('id', $this->company->id)->select('id', 'title', 'short_title', 'leaves_count')->first();
        $company_top_array = $company_top->toArray();
        $company_top_array['pid'] = 0;
        array_push($company_array, $company_top_array);
        $menus = (new Company())->toTree($company_array);
        $data['data'] = $menus == [] ? $company : $menus;
        return $this->response->array($data);
    }

    public function statManage($id = null, $month = null)
    {
        $this->check($id);
        if ($id == null) {
            $id = $this->company->id;
        }
        if ($month == null) {
            $lat_month = Carbon::now()->subMonth()->firstOfMonth();
            $year = $lat_month->year;
            $month = $lat_month->month;
        } else {
            $month = explode('-', $month);
            $year = $month[0];
            $month = $month[1];
        }
        if (!in_array($id, $this->company_ids)) {
            $id = $this->company->id;
        }
        if (date('d') < 3) {
            //TODO 临时用一下。月初1号没有数据。
            $month = $month - 1;
        }
//        Cache::forget('stat_manage_'.$id.'_'.$year.'_'.$month);
        $value = Cache::remember('stat_manage_' . $id . '_' . $year . '_' . $month, 60 * 24 * 30, function () use ($year, $month) {
            $companies = $this->company->children();
            if (count($companies)) {
                foreach ($companies as $company) {
                    $data[] = [
                        'id' => $company->id,
                        'name' => $company->title,
                        'value' => $company->statManageAvg($year, $month)
                    ];
                }
            } else {
                $data[] = [
                    'id' => $this->company->id,
                    'name' => $this->company->title,
                    'value' => $this->company->statManageAvg($year, $month)
                ];
            }
            return $data;
        });
        $data['data'] = $value;
        return $this->response->array($data);

    }

    public function statWarnings($id = null, $month = 6)
    {
        $this->check($id);
        if ($id == null) {
            $id = $this->company->id;
        }

        $months = get_last_months($month, null, 'Y-m-d');
        $this_month = date('Y-m-1');

        if (!in_array($id, $this->company_ids)) {
            $id = $this->company->id;
        }
//        Cache::forget('stat_warnings_'. $id . '_' . $month . '_'. $this_month);
        $value = Cache::remember('stat_warnings_' . $id . '_' . $month . '_' . $this_month, 60 * 24 * 30, function () use ($months, $this_month) {
            for ($i = 0; $i < count($months); $i++) {
                $start = $months[$i];
                if ($i < count($months) - 1) {
                    $end = $months[$i + 1];
                } else {
                    $end = $this_month;
                }
                $data[] = [
                    'start' => $start,
                    'end' => $end,
                    'name' => date('Y年m月', strtotime($start)),
                    'value' => $this->company->statWarningsCount($start, $end)
                ];
            }
            return $data;
        });
        $data['data'] = $value;
        return $this->response->array($data);

    }
}
