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
Route::get('deliveryProgress', 'DeliveryProgressController@index');

Route::group(['middleware' => 'auth.jwt'], function () {
    // untuk dropdown di form
    Route::get('agent/getList', 'AgentController@getList');
    Route::get('city/getList', 'CityController@getList');
    Route::get('company/byUser', 'CompanyController@byUser');
    Route::get('company/getList', 'CompanyController@getList');
    Route::get('customer/getList', 'CustomerController@getList');
    Route::get('deliveryStatus/getList', 'DeliveryStatusController@getList');
    Route::get('serviceType/getList', 'ServiceTypeController@getList');
    Route::get('delayCause/getList', 'DelayCauseController@getList');
    Route::get('deliveryType/getList', 'DeliveryTypeController@getList');
    Route::get('vehicleType/getList', 'VehicleTypeController@getList');
    Route::get('user/getList', 'UserController@getList');
    Route::get('user/getRoleList', 'UserController@getRoleList');
    Route::get('report/leadTime', 'ReportController@leadTime');
    Route::get('report/summary', 'ReportController@summary');
    Route::get('report/getFilterYear', 'ReportController@getFilterYear');


    // super admin only
    Route::group(['middleware' => 'role:11'], function() {
        Route::resource('company', 'CompanyController')->only(['index', 'store', 'destroy']);
        Route::resource('deliveryStatus', 'DeliveryStatusController')->except(['create', 'edit', 'show']);
        Route::resource('serviceType', 'ServiceTypeController')->except(['create', 'edit', 'show']);
        Route::resource('delayCause', 'DelayCauseController')->except(['create', 'edit', 'show']);
        Route::resource('deliveryType', 'DeliveryTypeController')->except(['create', 'edit', 'show']);
    });

    // superadmin & admin
    Route::group(['middleware' => 'role:11, 21'], function() {
        Route::resource('city', 'CityController')->only(['index', 'store', 'update', 'destroy']);
        Route::post('company/uploadLogo', 'CompanyController@uploadLogo');
        Route::resource('company', 'CompanyController')->only(['show', 'update']);
        Route::resource('user', 'UserController')->except(['create', 'edit']);
        Route::resource('agent', 'AgentController')->except(['create', 'edit']);
        Route::resource('customer', 'CustomerController')->except(['create', 'edit']);
    });

    // superadmin, admin, operator only
    Route::group(['middleware' => 'role:11, 21, 31'], function() {
        Route::post('deliveryProgress', 'DeliveryProgressController@store');
        Route::delete('domesticDeliveryItem/{domesticDeliveryItem}', 'DomesticDeliveryItemController@destroy');
        Route::get('domesticDelivery/search', 'DomesticDeliveryController@search');
        Route::get('domesticDelivery/printResi/{domesticDelivery}', 'DomesticDeliveryController@printResi');
        Route::get('domesticDelivery/printAwb/{domesticDelivery}', 'DomesticDeliveryController@printAwb');
        Route::resource('domesticDelivery', 'DomesticDeliveryController')->only(['store', 'update', 'destroy']);
        Route::get('invoice/print/{invoice}', 'InvoiceController@print');
        Route::resource('invoice', 'InvoiceController')->except(['create', 'edit']);

        Route::get('masterFare/search', 'MasterFareController@search');
        Route::resource('masterFare', 'MasterFareController')->except(['create', 'edit']);

        Route::get('masterFareCharter/search', 'MasterFareCharterController@search');
        Route::resource('masterFareCharter', 'MasterFareCharterController')->except(['create', 'edit']);

        Route::get('masterFarePacking/search', 'MasterFarePackingController@search');
        Route::resource('masterFarePacking', 'MasterFarePackingController')->except(['create', 'edit']);

        Route::resource('vehicleType', 'VehicleTypeController')->except(['create', 'edit', 'show']);
    });

    // Buat admin company
    Route::group(['middleware' => 'role: 21'], function() {
        Route::resource('companyBank', 'CompanyBankController')->except(['create', 'edit', 'show']);
    });

    // Buat admin company & operator
    Route::group(['middleware' => 'role: 21, 31'], function() {
        Route::post('report/send', 'ReportController@send');
    });

    // TODO : menu buat operator, customer & agent
    Route::group(['middleware' => 'role:11, 21, 31, 41, 51'], function() {
        Route::get('domesticDelivery', 'DomesticDeliveryController@index');
        // Route::get('deliveryProgress', 'DeliveryProgressController@index');
    });

    Route::get('checkAuth', 'AppController@checkAuth');
    Route::get('getNavigation', 'AppController@getNavigation');
    Route::post('logout', 'AuthController@logout');
});

Route::get('/{any}', 'AppController@index')->where('any', '.*');
