<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ccrp\EquipmentChangeApply;
use App\Models\CheckResult;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {

        return $content
            ->header('CCRP Admin')
            ->description('冷王 CCRP 控制中心')
            ->row(self::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(self::log());
                });
                $row->column(4, function (Column $column) {
                    $column->append(self::stat());
                });
                $row->column(4, function (Column $column) {
                    $column->append(self::environment());
                });
            });
    }


    public static function title()
    {
        return view('admin.dashboard.title');
    }

    public static function stat()
    {
        $check_result = CheckResult::where('status', 0)->selectRaw('count(1) as cnt,check_id')->groupBy('check_id')->get();
        $stats = [];
        foreach ($check_result as $item) {
            $stats[] = [
                'name' => $item->check->name,
                'value' => $item->cnt,
                'link' => 'check_results?_scope_=' . $item->check->id
            ];
        }
        $add = [
            'name' => '未处理参数变更单',
            'value' => EquipmentChangeApply::where('status', 0)->count(),
            'link' => 'equipment_change_applies?status=0'
        ];
        array_push($stats, $add);
        return view('admin.dashboard.stat', compact('stats'));
    }

    public static function environment()
    {
        $envs = [
            ['name' => 'PHP version', 'value' => 'PHP/' . PHP_VERSION],
            ['name' => 'Laravel version', 'value' => app()->version()],
            ['name' => 'CGI', 'value' => php_sapi_name()],
            ['name' => 'Uname', 'value' => php_uname()],
            ['name' => 'Server', 'value' => array_get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Cache driver', 'value' => config('cache.default')],
            ['name' => 'Session driver', 'value' => config('session.driver')],
            ['name' => 'Queue driver', 'value' => config('queue.default')],

            ['name' => 'Timezone', 'value' => config('app.timezone')],
            ['name' => 'Locale', 'value' => config('app.locale')],
            ['name' => 'Env', 'value' => config('app.env')],
            ['name' => 'URL', 'value' => config('app.url')],
        ];

        return view('admin.dashboard.environment', compact('envs'));
    }

    public static function log()
    {
        $logs = [
            ['name' => '20181205', 'value' => '巡检：主机电池故障'],
        ];

        return view('admin.dashboard.log', compact('logs'));
    }
}
