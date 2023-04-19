<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('users', 'UserController');

    $router->resource('teluser', 'TelegramUserController');

    $router->resource('history', 'TelegramHistoryController');    //发送历史
    $router->resource('order', 'TelegramOrderController');    //订单
    $router->resource('advertise', 'TelegramAdvertiseController');    //广告订单




});
