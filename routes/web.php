<?php


$router->get('/', function () use ($router) {
    return date('Y-m-d H:i:s');
});
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('/companies/current[/{id}]', [
        'as' => 'companies/current', 'uses' => 'CompaniesController@current'
    ]);
    $router->get('/companies[/{id}]', ['as' => 'api.ccrp.companies.index', 'uses' => 'CompaniesController@index']);
});
