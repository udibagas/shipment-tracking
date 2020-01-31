<?php

Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'auth'], function() {
    Route::post('logout', 'AuthController@logout');
    Route::get('domesticDelivery/search', 'DomesticDeliveryController@searchApi');
    Route::post('deliveryProgress', 'DeliveryProgressController@store');
});

Route::post('cekResi', 'DomesticDeliveryController@cekResi');
