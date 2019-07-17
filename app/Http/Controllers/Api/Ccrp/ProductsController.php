<?php

namespace App\Http\Controllers\Api\Ccrp;

use App\Http\Requests\Api\Ccrp\PrintLogRequest;
use App\Models\Ccrp\PrinterLog;

use App\Models\Ccrp\PrintLogTemplate;
use App\Models\Ccrp\Product;
use App\Transformers\Ccrp\PrinterLogDetailTransformer;
use App\Transformers\Ccrp\PrinterLogTransformer;
use App\Transformers\Ccrp\PrintLogTransformer;
use App\Transformers\Ccrp\ProductTransformer;


class ProductsController extends Controller
{
    private $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function index()
    {
        $this->check();
        $products = $this->model;
        if (request()->has('product_type')) {
            $product_type = request()->get('product_type');
            $products = $products->where('product_type', $product_type);
        }
        $products = $products->orderBy('product_id','desc')->paginate(request()->get('pagesize') ?? $this->pagesize);
        return $this->response->paginator($products, new ProductTransformer());
    }

    public function show($id)
    {
        $products=$this->model->find($id);
        return $this->response->item($products,new ProductTransformer());
    }

}
