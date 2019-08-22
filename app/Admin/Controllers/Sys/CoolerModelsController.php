<?php

namespace App\Admin\Controllers\Sys;

use App\Models\Ccrp\Sys\SysCoolerBrand;
use App\Models\Ccrp\Sys\SysCoolerModel;
use App\Models\Ccrp\Sys\SysCoolerType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use GuzzleHttp\Client;
use function App\Utils\microservice_access_encode;

class CoolerModelsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Ccrp\Sys\SysCoolerModel';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SysCoolerModel);

        $grid->model()->orderBy('popularity','desc');
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('type_id', __('Type id'));
        $grid->column('brand_id', __('Brand id'));
        $grid->column('description', __('Description'))->hide();
        $grid->column('power', __('Power'))->display(function($power) {
            return str_limit($power, 15, '...');
        });
        $grid->column('weight', __('Weight'));
        $grid->column('specifications', __('Specifications'));
        $grid->column('cool_volume', __('Cool volume'));
        $grid->column('cold_volume', __('Cold volume'));
        $grid->column('whole_volume', __('Whole volume'));
        $grid->column('is_medical', __('Is medical'));
        $grid->column('product_date', __('Product date'))->hide();
        $grid->column('body_type', __('Body type'));
        $grid->column('medical_licence', __('Medical licence'))->display(function($medical_licence) {
            return str_limit($medical_licence, 15, '...');
        });
        $grid->column('picture', __('Picture'))->display(function($picture) {
            return str_limit($picture, 8, '...');
        });
        $grid->column('temperature', __('Temperature'))->display(function($temperature) {
            return str_limit($temperature, 10, '...');
        });
        $grid->column('comment', __('Comment'));
        $grid->column('warmarea_count', __('Warmarea count'));
        $grid->column('popularity', __('Popularity'));
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();

        $grid->filter(function ($filter) {
//            $filter->disableIdFilter();
            $filter->equal('name', '型号');
            $filter->equal('brand_id', '品牌')->select(
                SysCoolerBrand::orderBy('popularity','desc')->pluck('name','id')
            );
        });
        $grid->fixColumns(0, -1);
        $grid->actions(function($actions){
            $actions->disableDelete(false);
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SysCoolerModel::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('type_id', __('Type id'));
        $show->field('brand_id', __('Brand id'));
        $show->field('description', __('Description'));
        $show->field('power', __('Power'));
        $show->field('weight', __('Weight'));
        $show->field('specifications', __('Specifications'));
        $show->field('cool_volume', __('Cool volume'));
        $show->field('cold_volume', __('Cold volume'));
        $show->field('whole_volume', __('Whole volume'));
        $show->field('is_medical', __('Is medical'));
        $show->field('product_date', __('Product date'));
        $show->field('body_type', __('Body type'));
        $show->field('medical_licence', __('Medical licence'));
        $show->field('picture', __('Picture'));
        $show->field('temperature', __('Temperature'));
        $show->field('comment', __('Comment'));
        $show->field('warmarea_count', __('Warmarea count'));
        $show->field('popularity', __('Popularity'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SysCoolerModel);

        $form->text('name', __('Name'));
        $form->radio('type_id', __('Type id'))->options(SysCoolerType::pluck('name','id'));
        $form->select('brand_id', __('Brand id'))->options(SysCoolerBrand::pluck('name','id'));
        $form->text('description', __('Description'));
        $form->text('power', __('Power'));
        $form->text('weight', __('Weight'));
        $form->text('specifications', __('Specifications'));
        $form->number('cool_volume', __('Cool volume'));
        $form->number('cold_volume', __('Cold volume'));
        $form->number('whole_volume', __('Whole volume'));
        $form->radio('is_medical', __('Is medical'))->options(SysCoolerModel::IS_MEDICAL);
        $form->date('product_date', __('Product date'))->default(date('Y-m-d'));
        $form->text('body_type', __('Body type'));
        $form->text('medical_licence', __('Medical licence'));
        $form->hidden('picture', __('Picture'));
        $form->display('图片')->with(function () {
            $picture = $this->picture;
            return view('admin.topic_images', ['picture' => $picture])->render();
        });
        $form->text('temperature', __('Temperature'));
        $form->text('comment', __('Comment'));
        $form->text('warmarea_count', __('Warmarea count'));
        $form->number('popularity', __('Popularity'));

        return $form;
    }

    public function upload()
    {
        if ($file = request()->file('zzz')) {
            $guzzle = new Client();
            $appkey = 'MICROSERVICE_TOPIC';
            $appsecret = '4C81FBF908AB0C8EEA9';
            $access = microservice_access_encode($appkey, $appsecret, []);
            $response = $guzzle->post('http://file-ms.coldyun.net/api/upload',
                [
                    'multipart' => [
                        [
                            'name' => 'action',
                            'contents' => 'test'
                        ],
                        [
                            'name' => 'file',
                            'contents' => fopen($file->getRealPath(), 'r')
                        ],
                    ],
                    'headers' => ['access' => $access]
                ]
            );
            return json_decode($response->getBody()->getContents())->url;
        }
        return '请选择图片';
    }
}
