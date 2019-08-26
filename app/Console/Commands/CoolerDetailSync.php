<?php

namespace App\Console\Commands;

use App\Models\Ccrp\Company;
use App\Models\Ccrp\Cooler;
use Illuminate\Console\Command;

class CoolerDetailSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccsc:cooler-detail-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步冰箱信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 在命令行打印一行信息
        $this->info("开始同步...");

        $companyIds=Company::where('id',26)->first()->ids();
        foreach (Cooler::whereNotIn('company_id',$companyIds)->get() as $key=>$cooler)
        {
            $this->info("开始同步...".$key);
            $extra['nipis_code'] = $cooler['cooler_cdc_sn'];
            $extra['cooler_type'] = $cooler['cooler_type']?Cooler::COOLER_TYPE[$cooler['cooler_type']]:'未知';
            $extra['come_from'] = $cooler['come_from'];
            $extra['comporation'] = $cooler['comporation'];
            $extra['model'] = $cooler['cooler_model'];
            $extra['product_sn'] = $cooler['product_sn'];
            $extra['product_date'] = $cooler['product_date'];
            $extra['arrive_date'] = $cooler['arrive_date'];
            $extra['use_date'] = $cooler['cooler_starttime'];
            $extra['is_medical'] =$cooler['is_medical']?Cooler::IS_MEDICAL[$cooler['is_medical']]:'未知';
            $extra['medical_permission'] = $cooler['medical_permission'];
            $extra['has_double_power'] = $cooler['has_double_power'];
            $extra['has_power_generator'] = $cooler['has_power_generator'];
            $extra['has_double_compressor'] = $cooler['has_double_compressor'];
            $extra['cooler_status'] = $cooler['cooler_status'];
            $extra['validate_name'] = $cooler['validate_name'];
            $extra['validate_status'] = $cooler['validate_status'];
            $cooler->saveDetails($extra);
        }

        $this->info("同步完成！");
    }
}
