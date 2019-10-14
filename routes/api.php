<?php

Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'auth.jwt'], function() {
    Route::post('logout', 'AuthController@logout');
    Route::get('domesticDelivery/search', 'DomesticDeliveryController@searchApi');
    Route::post('deliveryProgress', 'DeliveryProgressController@store');
});
