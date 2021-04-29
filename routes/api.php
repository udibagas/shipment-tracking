<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CompanyBankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DelayCauseController;
use App\Http\Controllers\DeliveryProgressController;
use App\Http\Controllers\DeliveryStatusController;
use App\Http\Controllers\DeliveryTypeController;
use App\Http\Controllers\DomesticDeliveryController;
use App\Http\Controllers\DomesticDeliveryItemController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MasterFareCharterController;
use App\Http\Controllers\MasterFareController;
use App\Http\Controllers\MasterFarePackingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleTypeController;
use App\User;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('cekResi', [DomesticDeliveryController::class, 'cekResi']);
Route::post('cekResi1', [DomesticDeliveryController::class, 'cekResi1']);


Route::middleware('api:sanctum')->group(function () {
    // auth related
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('checkAuth', [AppController::class, 'checkAuth']);
    Route::get('getNavigation', [AppController::class, 'getNavigation']);

    Route::post('deliveryProgress', [DeliveryProgressController::class, 'store']);
    Route::get('domesticDelivery/search', [DomesticDeliveryController::class, 'searchApi']);
    Route::get('report/getFilterYear', [ReportController::class, 'getFilterYear']);
    Route::get('report/leadTime', [ReportController::class, 'leadTime']);
    Route::get('report/summary', [ReportController::class, 'summary']);
    Route::get('user/getRoleList', [UserController::class, 'getRoleList']);

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
        Route::delete('domesticDeliveryItem/{domesticDeliveryItem}', [DomesticDeliveryItemController::class, 'destroy']);
        Route::get('domesticDelivery/search', [DomesticDeliveryController::class, 'search']);
        Route::get('domesticDelivery/printResi/{domesticDelivery}', [DomesticDeliveryController::class, 'printResi']);
        Route::get('domesticDelivery/printAwb/{domesticDelivery}', [DomesticDeliveryController::class, 'printAwb']);
        Route::get('invoice/print/{invoice}', [InvoiceController::class, 'print']);
        Route::get('masterFare/search', [MasterFareController::class, 'search']);
        Route::get('masterFareCharter/search', [MasterFareCharterController::class, 'search']);
        Route::get('masterFarePacking/search', [MasterFarePackingController::class, 'search']);

        Route::apiResources([
            'deliveryProgress' => DeliveryProgressController::class,
            'domesticDelivery' => DomesticDeliveryController::class,
            'invoice' => InvoiceController::class,
            'masterFare' => MasterFareController::class,
            'masterFareCharter' => MasterFareCharterController::class,
            'masterFarePacking' => MasterFarePackingController::class,
            'vehicleType' => VehicleTypeController::class,
        ]);
    });

    Route::middleware('role:' . User::ROLE_ADMIN . ',' . User::ROLE_OPERATOR)->group(function () {
        Route::post('report/send', [ReportController::class, 'send']);
    });

    Route::middleware('role:11, 21, 31, 41, 51')->group(function () {
        Route::get('domesticDelivery', [DomesticDeliveryController::class, 'index']);
    });
});
