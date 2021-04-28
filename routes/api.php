<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CompanyBankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DelayCauseController;
use App\Http\Controllers\DeliveryStatusController;
use App\Http\Controllers\DeliveryTypeController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth'], function () {
    Route::post('logout', 'AuthController@logout');
    Route::get('domesticDelivery/search', 'DomesticDeliveryController@searchApi');
    Route::post('deliveryProgress', 'DeliveryProgressController@store');
});

Route::post('cekResi', 'DomesticDeliveryController@cekResi');
Route::post('cekResi1', 'DomesticDeliveryController@cekResi1');


Route::middleware('api:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::middleware('role:' . User::ROLE_SUPERADMIN)->group(function () {
        Route::apiResources([
            'company' => CompanyController::class,
            'delayCause' => DelayCauseController::class,
            'deliveryStatus' => DeliveryStatusController::class,
            'deliveryType' => DeliveryTypeController::class,
            'serviceType' => ServiceTypeController::class,
        ]);
    });

    Route::middleware('role:' . User::ROLE_ADMIN)->group(function () {
        Route::apiResource('companyBank', CompanyBankController::class);
    });

    Route::middleware('role:' . User::ROLE_SUPERADMIN . ',' . User::ROLE_ADMIN)->group(function () {
        Route::apiResources([
            'agent' => AgentController::class,
            'city' => CityController::class,
            'company' => CompanyController::class,
            'customer' => CustomerController::class,
            'user' => UserController::class,
        ]);

        Route::post('company/uploadLogo', [CompanyController::class, 'uploadLogo']);
    });

    Route::middleware('role:' . User::ROLE_SUPERADMIN . ',' . User::ROLE_ADMIN . ', ' . User::ROLE_OPERATOR)->group(function () {
        Route::apiResources([
            // TODO
        ]);
    });
});
