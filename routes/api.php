<?php


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array']
], function ($api) {
    // 版本
    $api->get('version', function () {
        return '1.0.19.4.25';
    });
    $api->get('coolers/test', function (){
//        $table_name = '"sensor"."454678752"';
//        $sql2="select create_sensortable('454678752') as result;";
//        $rs2=\DB::connection('dbhistory')->select($sql2);
//        dd($rs2);
        dd(\App\Models\Ccrp\Collectorguanxi::first());
    });
    //测试：生成access发送请求
    $api->get('test/send', 'HelloController@send');

    //测试：生成access
    $api->get('test/access/{slug}', function ($slug) {
        $app = App\Models\App::where('slug', $slug)->first();
        $access = \App\Utils\microservice_access_encode($app->appkey, $app->appsecret, ['test' => 'hello word']);
        return $access;
    });

    $api->version('v1', [
        'middleware' => ['serializer:array', 'microservice_auth']
    ], function ($api) {
        //测试：接收请求，并解析access和附带info
        $api->get('test/receive', 'HelloController@receive');

        $api->group([
            'namespace' => 'Ccrp',
            'prefix' => 'ccrp',
        ], function ($api) {
            //单位树
            $api->get('companies/tree/{id?}', 'CompaniesController@tree')->name('api.ccrp.companies.tree');
            //单位下级单位
            $api->get('companies/branch/{id?}', 'CompaniesController@branch')->name('api.ccrp.companies.branch');
            // 当前单位
            $api->get('companies/current/{id?}', 'CompaniesController@current')->name('api.ccrp.companies.current');
            // 所有单位清单
            $api->get('companies/{id?}', 'CompaniesController@index')->name('api.ccrp.companies.index');
            // 管辖下级单位的管理水平报表
            $api->get('companies/stat/manage/{id?}/{month?}', 'CompaniesController@statManage')->name('api.ccrp.companies.stat_manage');
            $api->get('companies/stat/warnings/{id?}/{month?}', 'CompaniesController@statWarnings')->name('api.ccrp.companies.stat_warnings');
            //冰箱单位分类
            $api->resource('cooler_categories', CoolerCategoryController::class);
            //报警通道
            $api->get('warningers/get_warninger_types', 'WarningersController@getWarningerTypes');
            $api->resource('warningers', WarningersController::class);
            //报警器
            $api->get('ledspeakers/products', 'LedspeakersController@products');
            $api->get('ledspeakers/bind/{id}', 'LedspeakersController@bind');
            $api->resource('ledspeakers', LedspeakersController::class);
            //中继器
            $api->post('senders/warning_setting/{id}', 'SendersController@warningSetting');
            $api->resource('senders', SendersController::class);
            // 所有冰箱
            $api->get('coolers', 'CoolersController@index')->name('api.ccrp.coolers.index');
            $api->get('coolers/all', 'CoolersController@all')->name('api.ccrp.coolers.all');
            $api->get('coolers/cooler_type100', 'CoolersController@coolerType100')->name('api.ccrp.coolers.coolerType100');
            $api->get('coolers/{cooler}', 'CoolersController@show')->name('api.ccrp.coolers.show');
            $api->get('coolers/{cooler}/history', 'CoolersController@history')->name('api.ccrp.coolers.history');
            $api->post('coolers', 'CoolersController@store')->name('api.ccrp.coolers.store');
            $api->put('coolers/{id}', 'CoolersController@update')->name('api.ccrp.coolers.update');
            $api->get('sys/coolers', 'CoolersController@coolerType')->name('api.ccrp.coolers.cooler_type');
            $api->post('coolers/cooler_status/{id}', 'CoolersController@coolerStatus')->name('api.ccrp.coolers.cooler_status');
            // 所有探头
            $api->get('collectors', 'CollectorsController@index')->name('api.ccrp.collectors.index');
            $api->get('collectors/realtime', 'CollectorsController@realtime')->name('api.ccrp.collectors.realtime');
            $api->get('collectors/{collector}/history', 'CollectorsController@history')->name('api.ccrp.collectors.history');
            $api->get('collectors/{collector}', 'CollectorsController@show')->name('api.ccrp.collectors.show');
            $api->post('collectors', 'CollectorsController@store')->name('api.ccrp.collectors.store');
            $api->put('collectors/{id}', 'CollectorsController@update')->name('api.ccrp.collectors.update');
            $api->post('collector/uninstall/{id}', 'CollectorsController@uninstall')->name('api.ccrp.collectors.uninstall');
            // 是否包含手机号的联系人
            $api->get('contacts/{company_id}/has_phone/{phone}', 'ConcatsController@hasPhone')->name('api.ccrp.contacts.has_phone');
//             所有联系人
            $api->get('contacts/destroy/{id}', 'ConcatsController@destroy');
            $api->resource('contacts', ConcatsController::class);

            // 报警统计
            $api->get('warning_events/categories/{handled?}', 'WarningAllEventsController@categories')->name('api.ccrp.warning_all_events.categories');
            // 超温报警
            $api->get('warning_events/overtemp/list/{handled}', 'WarningEventsController@index')->name('api.ccrp.warning_events.index');
            $api->get('warning_events/overtemp/{event}', 'WarningEventsController@show')->name('api.ccrp.warning_events.show');
            $api->put('warning_events/overtemp/{event}', 'WarningEventsController@update')->name('api.ccrp.warning_events.update');
            // 断电报警
            $api->get('warning_events/poweroff/list/{handled}', 'WarningSenderEventsController@index')->name('api.ccrp.warning_sender_events.index');
            $api->get('warning_events/poweroff/{event}', 'WarningSenderEventsController@show')->name('api.ccrp.warning_sender_events.show');
            $api->put('warning_events/poweroff/{event}', 'WarningSenderEventsController@update')->name('api.ccrp.warning_sender_events.update');
            //报警发送记录
            $api->get('warning_sendlogs/list/{type?}', 'WarningSendlogsController@index')->name('api.ccrp.warning_sendlogs.list');
            $api->get('warning_sendlogs/{sendlog}', 'WarningSendlogsController@show')->name('api.ccrp.warning_sendlogs.show');
            //人工测温记录,查看或者签名
            $api->get('stat_manual_record/index/{cooler_id?}/{month?}', 'StatManualRecordsController@list')->name('api.ccrp.stat_manual_records.list');
            $api->get('stat_manual_records', 'StatManualRecordsController@create')->name('api.ccrp.stat_manual_records.create');
            $api->post('stat_manual_records', 'StatManualRecordsController@store')->name('api.ccrp.stat_manual_records.store');
            $api->get('stat_manual_records/list/{month?}', 'StatManualRecordsController@index')->name('api.ccrp.stat_manual_records.index');
            $api->get('stat_manual_records/show/{day?}/{session?}', 'StatManualRecordsController@show')->name('api.ccrp.stat_manual_records.show');
           //报警设置
            $api->resource('warning_settings', WarningSettingsController::class);
            //冷链变更
            $api->resource('equipment_change_applies', EquipmentChangeApplyController::class);
            $api->get('equipment_change_apply/statistics', 'EquipmentChangeApplyController@statistics');

            $api->get('equipment_change_types', 'EquipmentChangeApplyController@getChangeType');
            //第三方校准证书
            $api->get('jzzs', 'CertificationsController@index');
            $api->get('jzzs/{id}', 'CertificationsController@show');
            //巡检单
            $api->get('check_tasks','CheckTasksController@index');
            $api->get('check_tasks/{id}','CheckTasksController@show');
            //冷藏车
            $api->get('vehicles','VehiclesController@index');
            $api->get('vehicles/refresh/{vehicle_id}','VehiclesController@refresh');
            $api->get('vehicles/current/{vehicle_id}','VehiclesController@current');
            $api->get('vehicles/vehicle_temp','VehiclesController@vehicle_temp');
            $api->get('vehicles/vehicle_map','VehiclesController@vehicle_map');
            $api->get('printers','PrintersController@index');
            $api->get('printers/history_temp','PrintersController@printTemp');
            $api->get('printers/clear/{id}',function ($id){
                $resp= file_get_contents('http://pr01.coldyun.com/WPServer/clearorder?sn='.$id);
                return json_decode($resp,true);
            });
            $api->get('menus','MenusController@index');
            $api->post('export/callback', 'ExportController@callback')->name('api.ccrp.export_data');

            //CCrp数据报表
            $api->group([
                'namespace' => 'Reports',
                'prefix' => 'reports',
            ], function ($api) {
                $api->get('devices/statistic', 'DevicesController@statistic')->name('api.ccrp.reports.devices.statistic');
                $api->get('devices/stat_manages', 'DevicesController@statManage')->name('api.ccrp.reports.devices.stat_manage');
                $api->get('devices/stat_coolers', 'DevicesController@statCooler')->name('api.ccrp.reports.devices.stat_cooler');
                $api->post('devices/stat_cooler_history_temp', 'TemperatureController@statCoolerHistoryTemp')->name('api.ccrp.reports.devices.stat_cooler_history_temp');
                $api->get('temperatures/coolers_history_30/list/{month} ', 'TemperatureController@CoolerHistoryList')->name('api.ccrp.reports.coolers_history_30.list');
                $api->get('temperatures/coolers_history_30/{cooler_id}/{month}', 'TemperatureController@CoolerHistoryShow')->name('api.ccrp.reports.coolers_history_30.show');
                $api->get('warningers/statistics', 'WarningersController@statistics')->name('api.ccrp.reports.warningers.statistics');
                $api->get('login_logs/statistics', 'LoginLogsController@statistics')->name('api.ccrp.reports.login_logs.statistics');
                $api->get('login_logs/list', 'LoginLogsController@list')->name('api.ccrp.reports.login_logs.list');
                $api->get('coolers/logs', 'CoolersController@logs')->name('api.ccrp.reports.coolers.logs');
                $api->get('coolers/count_cooler_number', 'CoolersController@countCoolerNumber')->name('api.ccrp.reports.coolers.count_cooler_number');
                $api->get('coolers/count_cooler_volume', 'CoolersController@countCoolerVolume')->name('api.ccrp.reports.coolers.count_cooler_volume');
                $api->get('coolers/count_cooler_status', 'CoolersController@countCoolerStatus')->name('api.ccrp.reports.coolers.count_cooler_status');

                $api->get('companies/infomation/{slug}','CompaniesController@infomationDetail')->name('api.ccrp.reports.companies.info.detail');
                $api->get('companies/infomation','CompaniesController@infomation')->name('api.ccrp.reports.companies.infomation');
                //预警类型统计
                $api->get('warning_type_statistics', 'WarningersController@warningTypeStatistics')->name('api.ccrp.warning_type_statistics');
                //预警超温统计
                $api->get('cooler_temp_overrun', 'WarningersController@coolerTempOverRun')->name('api.ccrp.reports.cooler_temp_overrun');

            });
            //同步数据，获取data_id之后的新数据
//            $api->post('collectors/sync',function (){
//                return response(['_SERVER'=>json_encode($_SERVER)]);
//            });
//            $api->post('collectors/sync', 'CollectorsController@sync')->name('api.collectors.sync');
            //同步基础数据 -- collector
//            $api->post('tables_syncs', 'TablesSyncsController@index')->name('api.table_syncs.index');

        });


    });

});
