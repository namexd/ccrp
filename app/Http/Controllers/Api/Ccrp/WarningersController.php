<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\WarninerRequest;
use App\Models\Ccrp\Company;
use App\Models\Ccrp\Warninger;
use App\Transformers\Ccrp\WarningerTransformer;

class WarningersController extends Controller
{
    private $model;

    public function __construct(Warninger $warninger)
    {
        $this->model = $warninger;
    }

    public function index()
    {
        $this->check();
        $warninger = $this->model->whereIn('company_id', $this->company_ids);
        if ($keyword = request()->get('keyword')) {
            $warninger = $warninger->where('warninger_name', 'like', '%'.$keyword.'%');
        }
        $warninger = $warninger->orderBy('warninger_id', 'desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($warninger, new WarningerTransformer())
            ->addMeta('warninger_body_limit',[
                'warninger_body'=>$this->company->warninger_body_limit,
                'warninger_body_level_2'=>1,
                'warninger_body_level_3'=>1,
            ]);
    }

    public function show($id)
    {
        $this->check();
        $warning = $this->model->find($id);
        return $this->response->item($warning, new WarningerTransformer());
    }

    public function update($id)
    {
        $this->check();
        $request = request()->all();
        $request['utime'] = time();
        $warninger = $this->model->find($id);
        $result = $warninger->update($request);
        if ($result) {
            return $this->response->item($warninger, new WarningerTransformer());
        } else {
            return $this->response->errorInternal('修改失败');
        }
    }

    public function store(WarninerRequest $request)
    {
        $this->check();
        $request['set_time'] = time();
        $request['ctime'] = time();
        $request['set_uid'] = $this->user->id;
        $request['company_id'] = $this->company->id;
        if (count($request['warninger_body'])>3|| count($request['warninger_body_level2'])>3 || count($request['warninger_body_level3'])>3)
            {
                return $this->response->errorBadRequest('报警联系人设置不可超过三个');
            }
        $result = $this->model->create($request->all());
        if ($result) {
            return $this->response->item($result, new WarningerTransformer());
        } else {
            return $this->response->errorInternal('添加失败');
        }
    }

    public function destroy($id)
    {
        $warninger = $this->model->find($id);
        if (!$warninger) {
            return $this->response->errorBadRequest('该报警通道不存在');
        }
        if ($warninger->bindtimes > 0) {
            return $this->response->errorBadRequest('该报警通道已被使用，无法删除');
        }
        $warninger->delete();
        return $this->response->noContent();
    }

    public function getWarningerTypes()
    {
        return $this->response->array($this->model->getWarningTypes());
    }
}
