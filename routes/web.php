<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::get('deliveryProgress', 'DeliveryProgressController@index');

Route::group(['middleware' => 'auth'], function () {
    // untuk dropdown di form
    Route::get('user/getRoleList', 'UserController@getRoleList');
    Route::get('report/leadTime', 'ReportController@leadTime');
    Route::get('report/summary', 'ReportController@summary');
    Route::get('report/getFilterYear', 'ReportController@getFilterYear');


    // superadmin, admin, operator only
    Route::group(['middleware' => 'role:11, 21, 31'], function () {
        Route::resource('deliveryProgress', 'DeliveryProgressController')->only(['store', 'update']);
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

    // Buat admin company & operator
    Route::group(['middleware' => 'role: 21, 31'], function () {
        Route::post('report/send', 'ReportController@send');
    });

    // TODO : menu buat operator, customer & agent
    Route::group(['middleware' => 'role:11, 21, 31, 41, 51'], function () {
        Route::get('domesticDelivery', 'DomesticDeliveryController@index');
        // Route::get('deliveryProgress', 'DeliveryProgressController@index');
    });

    Route::get('checkAuth', 'AppController@checkAuth');
    Route::get('getNavigation', 'AppController@getNavigation');
    Route::post('logout', 'AuthController@logout');
});

Route::get('/', 'AppController@index')->name('login'); // ini buat redirect kalau unauthorized
Route::get('/{any}', 'AppController@index')->where('any', '.*');
