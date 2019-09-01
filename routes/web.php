<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
ROLE USER:
11 : SUPER ADMIN
21 : ADMIN
31 : OPERATOR
41 : CUSTOMER
51 : AGENT
*/

Route::post('login', 'AuthController@login');

Route::group(['middleware' => 'auth.jwt'], function () {
    // untuk dropdown di form
    Route::get('agent/getList', 'AgentController@getList');
    Route::get('city/getList', 'CityController@getList');
    Route::get('company/getList', 'CompanyController@getList');
    Route::get('customer/getList', 'CustomerController@getList');
    Route::get('deliveryStatus/getList', 'DeliveryStatusController@getList');
    Route::get('serviceType/getList', 'ServiceTypeController@getList');
    Route::get('deliveryType/getList', 'DeliveryTypeController@getList');
    Route::get('user/getList', 'UserController@getList');
    Route::get('user/getRoleList', 'UserController@getRoleList');
    Route::get('report/leadTime', 'ReportController@leadTime');


    // super admin only
    // Route::group(['middleware' => 'role:11'], function() {
        Route::resource('company', 'CompanyController')->except(['create', 'edit']);
        Route::resource('deliveryStatus', 'DeliveryStatusController')->except(['create', 'edit', 'show']);
        Route::resource('serviceType', 'ServiceTypeController')->except(['create', 'edit', 'show']);
        Route::resource('deliveryType', 'DeliveryTypeController')->except(['create', 'edit', 'show']);
    // });

    // superadmin & admin
    // Route::group(['middleware' => 'role:11, 21'], function() {
        Route::resource('user', 'UserController')->except(['create', 'edit']);
    // });

    // superadmin, admin, operator only
    // Route::group(['middleware' => 'role:11, 21, 31'], function() {
        Route::resource('agent', 'AgentController')->except(['create', 'edit']);
        Route::resource('customer', 'CustomerController')->except(['create', 'edit']);
    // });

    // TODO : menu buat operator, customer & agent
    // Route::group(['middleware' => 'role:11, 21, 31, 41, 51'], function() {
        Route::post('deliveryProgress', 'DeliveryProgressController@store');
        Route::resource('domesticDelivery', 'DomesticDeliveryController')->except(['create', 'edit']);
    // });

    Route::get('checkAuth', 'AppController@checkAuth');
    Route::post('logout', 'AuthController@logout');
});

Route::get('/{any}', 'AppController@index')->where('any', '.*');
