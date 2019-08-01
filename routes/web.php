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

Route::post('login', 'AuthController@login');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('checkAuth', 'AppController@checkAuth');
    Route::post('logout', 'AuthController@logout');
    Route::resource('user', 'UserController')->except(['create', 'edit']);
    Route::resource('agent', 'AgentController')->except(['create', 'edit']);
    Route::resource('customer', 'CustomerController')->except(['create', 'edit']);
    Route::resource('company', 'CompanyController')->except(['create', 'edit']);
    Route::resource('deliveryStatus', 'DeliveryStatusController')->except(['create', 'edit', 'show']);
    Route::resource('serviceType', 'ServiceTypeController')->except(['create', 'edit', 'show']);
    Route::resource('deliveryType', 'DeliveryTypeController')->except(['create', 'edit', 'show']);
});

Route::get('/{any}', 'AppController@index')->where('any', '.*');
