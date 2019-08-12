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

});
