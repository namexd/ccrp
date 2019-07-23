<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\CompanyRequest;
use App\Http\Requests\Api\Ccrp\Setting\CompanySettingRequest;
use App\Models\Ccrp\Area;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\User;
use App\Models\Ccrp\WarningEvent;
use App\Models\Ccrp\WarningSenderEvent;
use App\Models\Ocenter\UCenterMember;
use App\Transformers\Ccrp\CompanyDetailTransformer;
use App\Transformers\Ccrp\CompanyInfoTransformer;
use App\Transformers\Ccrp\CompanyListTransformer;
use App\Transformers\Ccrp\CompanyTransformer;
use App\Transformers\Ccrp\CoolerCategoryTransformer;
use function App\Utils\domain_fix;
use function App\Utils\get_last_months;
use function App\Utils\get_month_first;
use function App\Utils\get_month_last;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public $model;

    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    public function index(CompanyRequest $request, $id = null)
    {
        $this->check($id);

        if ($this->company->isProvinceCdc()) {
            $ids = (new Company())->getSonCompanyIds($id ?? $this->company->id);
        } else {
            $ids = $this->company_ids;
        }


        $companies = Company::whereIn('id', $ids)->where('status', 1);

        if ($keyword=$request->get('keyword'))
        {
            $companies=$companies->where('title','like','%'.$keyword.'%');
        }
        if (!$this->company->isProvinceCdc() and isset($request->hidden) and $request->hidden == 'admin') {
            $companies->where('cdc_admin', 0);
        } elseif ($this->company->isProvinceCdc()) {
            $companies->where('cdc_admin', 1);
        }

        $companies = $companies->orderBy('pid', 'asc')->orderBy('title', 'asc')->paginate($request->pagesize??$this->pagesize);

        if ($id == null) {
            $current = $this->company;
        } else {
            $ids = (new Company())->getSubCompanyIds($id);
            if (in_array($id, $ids)) {
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

        return $this->response->paginator($companies, new CompanyListTransformer())->addMeta('current', $current_company);
    }

    public function current($id = null)
    {
        $this->check($id);
//        Cache::forget('companies.'.$this->company->id.'.current');
        $company = Cache::remember('companies.'.$this->company->id.'.current', 10, function () use ($id) {
            return $this->refresh($id);
        });
        return $this->response->item($company, new CompanyInfoTransformer());
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

    public function branch($id = null)
    {
        $this->check($id);
        $company = Company::branchListWithOrders($this->company->id, null, ['id', 'pid', 'title', 'short_title', 'leaves_count']);
        $company_array = $company->toArray();
        $company_top = Company::where('id', $this->company->id)->select('id', 'pid', 'title', 'short_title', 'leaves_count')->first();
        $company_top_array = $company_top->toArray();
        if ($id == null) {
            $company_top_array['pid'] = 0;
        }
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
        $value = Cache::remember('stat_manage_'.$id.'_'.$year.'_'.$month, 60 * 24 * 30, function () use ($year, $month) {
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
        $value = Cache::remember('stat_warnings_'.$id.'_'.$month.'_'.$this_month, 60 * 24 * 30, function () use ($months, $this_month) {
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

    public function show($id)
    {
        return $this->response->item($this->model->find($id),new CompanyDetailTransformer());

    }

    public function store(CompanySettingRequest $request)
    {
        $this->check();
        if ($pid = $request->pid)
            $company = $this->model->find($pid);
        else
            $company = $this->company;
        $request['reg_type'] = 'username';
        $request['binding_domain'] = domain_fix();
        $request['cdc_level'] = $company['cdc_level'] + 1;
        if ($company['cdc_level'] >= 1)
            $request['area_level1_id'] = $company['area_level1_id'];
        if ($company['cdc_level'] >= 2)
            $request['area_level2_id'] = $company['area_level2_id'];
        if ($company['cdc_level'] >= 3)
            $request['area_level3_id'] = $company['area_level3_id'];

        $request['company_group'] = $company['company_group'];

        $result = $this->model->create($request->all());
        return $this->response->item($result, new CompanyListTransformer())->setStatusCode(201);
    }
    
    public function update($id,CompanySettingRequest $request)
    {
        $this->check();
        if (is_array($request['phone'])) {
            $request['phone'] = implode(',', $request['phone']);
        }
        $company = $this->model->find($id);
        $request['reg_type'] = 'username';
        $request['binding_domain'] = domain_fix();

        if ($company['pid'] != $request['pid']) {
            $parent = $this->model->selectRaw('id,pid,cdc_admin,cdc_level,area_level1_id,area_level2_id,area_level3_id,area_level4_id')->find($request['pid']);
            $request['cdc_level'] = $parent['cdc_level'] + 1;
            if ($request['area_level4_id']) {
                $request['area_level3_id'] = $parent['area_level3_id'];
                $request['area_level2_id'] = $parent['area_level2_id'];
                $request['area_level1_id'] = $parent['area_level1_id'];
            } elseif ($request['area_level3_id']) {
                $request['area_level4_id'] = 0;
                $request['area_level2_id'] = $parent['area_level2_id'];
                $request['area_level1_id'] = $parent['area_level1_id'];
            } elseif ($request['area_level2_id']) {
                $request['area_level4_id'] = 0;
                $request['area_level3_id'] = 0;
                $request['area_level1_id'] = $parent['area_level1_id'];
            } elseif ($request['area_level1_id']) {
                $request['area_level4_id'] = 0;
                $request['area_level3_id'] = 0;
                $request['area_level2_id'] = 0;
            }
        }
        $result =$company->update($request->all());
        if ($result) {
            if ($company['title'] <> $result['title']) {
                $data_user['company'] = $result['title'];
                $company->users()->update($data_user);
            }
          return $this->response->item($company,new CompanyDetailTransformer());
        } else {
            $this->error('修改失败');
        }
    }


    public function resetPassword($id)
    {
        $company_id = $id;
        $company = $this->model->find($company_id);
        if ($company) {
            $where['username'] = $company['username'];
            $where['company_id'] = $company_id;
            $result = User::where($where)->update(['password'=>(new User())->user_md5($company['password'])]); //重置密码
            //更新UCENTER密码
            $ocenter = UCenterMember::where('username',$company['username'])->update(['password'=>(new User())->user_md5($company['password'])]); //重置密码
            if ($result) {
             return $this->response->noContent();
            } else {
                return $this->response->errorMethodNotAllowed('密码没有任何修改');
            }
        }
    }
    public function subAdminCompanies()
    {
        $this->check();
        $company = $this->company;

        if ($company->cdc_admin == 1) {
            $ids = $company->ids(1);
            $companies = $this->model->whereIn('id', $ids)->get();
            $submap2['cdc_level'] = $company['cdc_level'] + 1;
            $submap2['area_level'.$company['cdc_level'].'_id'] = $company['area_level'.$company['cdc_level'].'_id'];
            $sub_company = $this->model->where($submap2)->count();
            $default = array('sup_company' => $company['title'],
                'sup_area' => $company['area_level'.$company['cdc_level'].'_id'],
                'password' => str_replace("'", '', Area::get_area_pinyin($company['area_level'.$company['cdc_level'].'_id']).'123456'
                ));
            if ($company['cdc_level'] <= 3) {

                $default['item_name'] = '行政区域';
                $default['item_type'] = 'select';

                $default['username'] = $company['area_level'.$company['cdc_level'].'_id'].sprintf("%02d", ($sub_company + 1));
            } else {
                $default['item_name'] = '行政单位';
                $default['item_type'] = 'text';
                $default['username'] = $company['area_level'.$company['cdc_level'].'_id'].sprintf("%02d", ($sub_company + 1));
            }
            if (request()->get('admin') == 1)
                $default['cdc_admin'] = 1;
            else
                $default['cdc_admin'] = 0;
            $default['pid'] = $company['id'];//默认上级单位
            $default['company_type'] = 3;//默认上级单位
            return $this->response->collection($companies, new CompanyListTransformer())
                ->addMeta('name', 'area_level'.($company['cdc_level'] + 1).'_id')
                ->addMeta('list', Area::getListByConditions(['parent_id' => $company['area_level'.$company['cdc_level'].'_id']]))
                ->addMeta('sub_company_count', $sub_company)
                ->addMeta('sup_area', $company['area_level'.$company['cdc_level'].'_id'])
                ->addMeta('default', $default);
        } else {
            return $this->response->errorMethodNotAllowed('非疾控单位');
        }

    }

}
