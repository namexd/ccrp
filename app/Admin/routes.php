<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('sys-cooler-brands', Sys\CoolerBrandsController::class);
    $router->resource('sys-cooler-models', Sys\CoolerModelsController::class);
    $router->resource('task-remind-login-tasks', Reports\TaskRemindLoginTasksController::class);
    $router->post('sys-cooler-models/image/upload', 'Sys\CoolerModelsController@upload');
    //ucenter
    $router->resource('ucenter/users', UsersController::class);
    $router->resource('ucenter/roles', RolesController::class);
    $router->resource('ucenter/permissions', PermissionsController::class);
    $router->resource('ucenter/menus', MenusController::class);

    $router->post('users/remove_roles','UsersController@removeRoles')->name('users.remove_roles');
    //ccrp
    $router->group([
        'prefix' => 'ccrp',
        'namespace' => 'Ccrp',
        'middleware' => config('admin.route.middleware'),
    ], function (Router $router) {
        $router->resource('deliverorders', DeliverordersController::class);
        $router->resource('printerlogs', PrinterlogsController::class);
        $router->resource('printerlog_approve', PrinterlogApprovesController::class);
        $router->resource('collectors', CollectorsController::class);
        $router->resource('senders', SendersController::class);
        $router->resource('contacts', ContactsController::class);
        $router->resource('coolers', CoolersController::class);
        $router->resource('cooler_details', CoolerDetailsController::class);
        $router->resource('ceshi/sensor', CeshiSensorsController::class);
        $router->get('sender_instructs/lists/{sender}', 'SenderInstructsController@lists')->name('ccrp.sender_instruct');
        $router->get('sender_instructs/lists/{sender}/create', 'SenderInstructsController@create_sender')->name('ccrp.sender_instruct.crteate_sender');
        $router->post('sender_instructs/lists/{sender}/create', 'SenderInstructsController@created_sender')->name('ccrp.sender_instruct.crteated_sender');
        $router->resource('sender_instructs', SenderInstructsController::class);
        $router->get('senders/{key}/{value}', 'SendersController@show')->name('ccrp.senders.show');
        $router->resource('users', UsersController::class);
        $router->get('login/{company}', 'UsersController@login')->name('ccrp.login');
        $router->get('login_ccrps/{company}', 'UsersController@loginCcrps')->name('ccrp.login.ccrps');
        $router->get('login_wechat/{company}', 'UsersController@loginWechat')->name('ccrp.login.wechat');
        $router->resource('companies', CompaniesController::class);
        $router->get('companies/create_cdc_admin_by_area/{area}', 'CompaniesController@createCdcAdminByArea')->name('ccrp.companies.create_cdc_admin_by_area');
        $router->post('companies/create_cdc_admin_by_area/{area}', 'CompaniesController@createCdcAdminByArea')->name('ccrp.companies.create_cdc_admin_by_area');
        $router->get('settings/companies/check/{setting}/{company}', 'SettingsController@check')->name('ccrp.settings_check');
        $router->get('settings/companies/{company}', 'SettingsController@company')->name('ccrp.settings_company');
        $router->get('companies/defaultSetting/{company}', 'SettingsController@company')->name('ccrp.settings_company');
        $router->get('companies/tools/{company}', 'CompaniesController@tools')->name('ccrp.company.tools');
        $router->get('companies/settings/default/{company}', 'SettingsController@default')->name('ccrp.company.settings.default');
        $router->post('companies/tag', 'CompaniesController@tag');
        $router->resource('tags', TagsController::class);
        $router->resource('company_settings', CompanySettingsController::class);
        $router->resource('warningers', WarningersController::class);
        $router->get('certifications/createBatch', 'CertificationsController@createBatch')->name('ccrp.certifications.createBatch');
        $router->post('certifications/saveBatch', 'CertificationsController@saveBatch')->name('ccrp.certifications.saveBatch');
        $router->resource('certifications', CertificationsController::class);
        $router->resource('files', FilesController::class);
        $router->resource('warning_sendlogs', WarningSendlogsController::class);
        $router->post('cooler_validates/update_to_cooler','CoolerValidatesController@updateToCooler')->name('ccrp.cooler_validate.update_to_cooler');
        $router->resource('cooler_validates', CoolerValidatesController::class);
        $router->resource('areas', AreasController::class);
        $router->resource('transfer_collectors', TransferCollectorsController::class);
        $router->get('sys', 'Sys\IndexController@index');
        $router->resource('sys/cooler_brands', Sys\CoolerBrandsController::class);
        $router->resource('sys/cooler_models', Sys\CoolerModelsController::class);
        $router->resource('sys/settings', Sys\SettingsController::class);
        $router->resource('sys/company/photos', Sys\CompanyPhotosController::class);
        $router->resource('sys/company/details', Sys\CompanyDetailsController::class);
        $router->resource('sys/cooler/details', Sys\CoolerDetailsController::class);
        $router->resource('sys/cooler/photos', Sys\CoolerPhotosController::class);
        $router->resource('reports/stat_monthly', Reports\StatMonthlyController::class);


        // 单位个性化设置检测：探头离线开启设置
        $router->get('collectors/check_offline_status/{setting}/{company}', 'CollectorsController@checkOfflineStatus')->name('ccrp.collectors.check_offline_status');
        // 单位个性化设置检测：探头离线间隔时间设置
        $router->get('collectors/check_offline_span/{setting}/{company}', 'CollectorsController@checkOfflineSpan')->name('ccrp.collectors.check_offline_span');
        // 单位个性化设置检测：探头报警状态设置
        $router->get('collectors/check_warning_status/{setting}/{company}', 'CollectorsController@checkWarningStatus')->name('ccrp.collectors.check_warning_status');
        // 单位个性化设置检测：冷冻探头设置延迟时间
        $router->get('warning_settings/check_temp_warning_last/{setting}/{company}', 'WarningSettingsController@checkTempWarningLast')->name('ccrp.warning_settings.check_temp_warning_last');
        // 单位个性化设置检测：冷冻探头上限
        $router->get('warning_settings/check_temp/cold_max/{setting}/{company}', 'WarningSettingsController@checkTemp')->name('ccrp.warning_settings.check_temp.cold_max');
        // 单位个性化设置检测：冷藏探头上下限
        $router->get('warning_settings/check_temp/cool_range/{setting}/{company}', 'WarningSettingsController@checkTempCoolRange')->name('ccrp.warning_settings.check_temp.cool_range');
        // 单位个性化设置检测：冷冻探头上下限
        $router->get('warning_settings/check_temp/cold_range/{setting}/{company}', 'WarningSettingsController@checkTempColdRange')->name('ccrp.warning_settings.check_temp.cold_range');
        // 单位个性化设置检测：探头温度报警批量开启关闭
        $router->get('warning_settings/check_temp_warning/{setting}/{company}', 'WarningSettingsController@checkTempWarning')->name('ccrp.warning_settings.check_temp_warning');
        // 单位个性化设置检测：设置延迟时间
        $router->get('sender_warning_settings/check_warning_last/{setting}/{company}', 'SenderWarningSettingsController@checkWarningLast')->name('ccrp.sender_warning_settings.check_warning_last');
        // 单位个性化设置检测：冷冻探头上限设置
        $router->get('companies/check_remind_login/{setting}/{company}', 'CompaniesController@checkRemindLogin')->name('ccrp.company.check_remind_login');
        // 单位个性化设置检测：变更单需要审核
        $router->get('companies/check_equipment_change_need_verify/{setting}/{company}', 'CompaniesController@checkEquipmentChangeNeedVerify')->name('ccrp.company.check_equipment_change_need_verify');
        // 单位个性化设置检测：关闭微信端功能
        $router->get('companies/check_forbidden_weixin/{setting}/{company}', 'CompaniesController@checkForbiddenWeixin')->name('ccrp.company.check_forbidden_weixin');
        // 单位个性化设置检测：开启冷链设备的货位功能（存放疫苗标识）
        $router->get('companies/check_cooler_has_vaccine_tags_manage/{setting}/{company}', 'CompaniesController@checkCoolerHasVaccineTagsManage')->name('ccrp.company.check_cooler_has_vaccine_tags_manage');
        // 单位个性化设置检测：是否有第三方校准证书
        $router->get('collectors/check_has_certification/{setting}/{company}', 'CollectorsController@checkHasCertification')->name('ccrp.collectors.check_has_certification');
        // 单位个性化设置检测：人工测温记录 检测
        $router->get('company_has_functions/check_manual_records/{setting}/{company}', 'CompanyHasFunctionsController@checkManualRecords')->name('ccrp.company_has_functions.check_manual_records');
        // 单位个性化设置检测：人工测温记录 设置
        $router->post('company_has_functions/update_rows', 'CompanyHasFunctionsController@updateRows')->name('ccrp.company_has_functions.update_rows');
        // 单位个性化设置检测：一级报警联系人人数
        $router->get('companies/check_warninger_body_limit/{setting}/{company}', 'CompaniesController@checkWarningerBodyLimit')->name('ccrp.company.check_warninger_body_limit');
        // 单位个性化设置检测
        $router->get('companies/check_company_use_settings/{setting}/{company}', 'CompaniesController@checkCompanyUseSettings')->name('ccrp.company.check_company_use_settings');
        // 单位个性化设置
        $router->post('companies/set_company_use_settings', 'CompaniesController@setCompanyUseSettings')->name('ccrp.company.set_company_use_settings');

        //是否开启冰箱整体离线巡检
        $router->get('coolers/offline_check/{setting}/{company}', 'CoolersController@offlineCheck')->name('ccrp.cooler.offline_check');
        $router->post('coolers/set_offline_check', 'CoolersController@setOfflineCheck')->name('ccrp.cooler.set_offline_check');

        //是否开启室温人工签名
        $router->get('coolers/room_sign/{setting}/{company}', 'CoolersController@roomSign')->name('ccrp.cooler.room_sign');
        $router->post('coolers/set_room_sign', 'CoolersController@setRoomSign')->name('ccrp.cooler.set_room_sign');

        // 自动切换电话预警
        $router->get('warningers/check_auto_change/{setting}/{company}', 'WarningersController@checkAutoChange')->name('ccrp.warningers.check_auto_change');
        // 报警方式（短信电话）
        $router->get('warningers/check_warninger_type/{setting}/{company}', 'WarningersController@checkWarningerType')->name('ccrp.warningers.check_warninger_type');
        // 字段设置：探头
        $router->post('collectors/update_field', 'CollectorsController@updateField')->name('ccrp.collectors.update_field');
        // 字段设置：预警通道
        $router->post('warningers/update_field', 'WarningersController@updateField')->name('ccrp.warningers.update_field');
        // 字段设置：报警设置
        $router->post('warning_settings/update_field', 'WarningSettingsController@updateField')->name('ccrp.warning_settings.update_field');
        // 字段设置：断电报警设置
        $router->post('sender_warning_settings/update_field', 'SenderWarningSettingsController@updateField')->name('ccrp.sender_warning_settings.update_field');
        // 字段设置：单位
        $router->post('companies/update_field', 'CompaniesController@updateField')->name('ccrp.companies.update_field');
        // 单位登录提醒
        $router->post('companies/remind_login', 'CompaniesController@createRemindLogin')->name('ccrp.companies.create_remind_login');
        $router->post('companies/remind_login/remove', 'CompaniesController@removeRemindLogin')->name('ccrp.companies.remove_remind_login');
        $router->resource('stat_manages', StatManageController::class);
        $router->resource('stat_coolers', StatCoolerController::class);
        $router->any('stat_manages_export', 'StatManageController@export');
        $router->any('stat_coolers_export', 'StatCoolerController@export');

        $router->get('equipment_change_applies/create', 'EquipmentChangeApplyController@create')->name('equipment_change_apply.create');
        $router->post('equipment_change_applies/batch', 'EquipmentChangeApplyController@batch');
        $router->resource('equipment_change_applies', EquipmentChangeApplyController::class);
        $router->resource('menus', MenusController::class);

        $router->resource('employees', EmployeesController::class);


    });
});
