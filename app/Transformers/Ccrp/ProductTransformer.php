<?php

namespace App\Transformers\Ccrp;

use App\Models\Ccrp\Printer;
use App\Models\Ccrp\PrinterLog;
use App\Models\Ccrp\Product;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{

    public function transform(Product $product)
    {
        $result = [
            'product_id'=>$product->product_id,
            'product_ model'=>$product->product_model,
            'supplier_id'=>$product->subtitle,
            'supplier_product_model'=>$product->supplier_product_model,
            'product_type'=>$product->product_type,
            'safe_collector_volt_warning'=>$product->safe_collector_volt_warning,
            'safe_collector_volt_warning_mail'=>$product->safe_collector_volt_warning_mail,
            'safe_collector_volt_low'=>$product->safe_collector_volt_low,
            'safe_collector_volt_high'=>$product->safe_collector_volt_high,
            'cold_safe_collector_volt_low'=>$product->cold_safe_collector_volt_low,
            'status'=>$product->status,
        ];
        return $result;
    }


}