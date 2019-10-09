<?php

Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'auth.jwt'], function() {
    Route::post('logout', 'AuthController@logout');
    Route::get('domesticDelivery/search', 'DomesticDeliveryController@search');
    Route::post('deliveryProgress', 'DeliveryProgressController@store');
});
