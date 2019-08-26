<?php

namespace App\Console\Commands;

use App\Models\CheckTask;
use Illuminate\Console\Command;

class BuildCheckTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ccsc:build-check-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建巡检单';

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
     * @param CheckTask $checkTask
     * @return mixed
     */
    public function handle(CheckTask $checkTask)
    {
        $company_id = $this->ask('输入指定单位ID?');
        // 在命令行打印一行信息
        $this->info("开始计算...");

        $checkTask->buildTask($company_id);

        $this->info("成功生成！");
    }
}
