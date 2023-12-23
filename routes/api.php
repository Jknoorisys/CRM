<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ManageUserController;
use App\Http\Controllers\api\master\ManageCityController;
use App\Http\Controllers\api\master\ManageContactStatusController;
use App\Http\Controllers\api\master\ManageCountryController;
use App\Http\Controllers\api\master\ManageDesignationController;
use App\Http\Controllers\api\master\ManageLeadTypeController;
use App\Http\Controllers\api\master\ManageTaskStatusController;
use App\Http\Controllers\api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('set.locale')->group(function () {
    // Login
    Route::post('login' , [AuthController::class, 'login']);

    Route::middleware('jwt.verify')->group(function () {
        Route::post('profile' , [ProfileController::class, 'getProfile']);
        Route::post('change-password' , [ProfileController::class, 'changePassword']);

        // Manage Users
        Route::post('user/list' , [ManageUserController::class, 'list']);
        Route::post('user/add' , [ManageUserController::class, 'add']);
        Route::post('user/view' , [ManageUserController::class, 'view']);
        Route::post('user/update' , [ManageUserController::class, 'update']);
        Route::post('user/change-status' , [ManageUserController::class, 'changeStatus']);
        Route::post('user/delete' , [ManageUserController::class, 'delete']);
        Route::post('user/reset-password' , [ManageUserController::class, 'resetPassword']);

        // Manage Masters
        Route::prefix('master')->group(function () {
            // Manage Country
            Route::post('country/list' , [ManageCountryController::class, 'list']);
            Route::post('country/add' , [ManageCountryController::class, 'add']);
            Route::post('country/view' , [ManageCountryController::class, 'view']);
            Route::post('country/update' , [ManageCountryController::class, 'update']);
            Route::post('country/change-status' , [ManageCountryController::class, 'changeStatus']);
            Route::post('country/delete' , [ManageCountryController::class, 'delete']);

            // Manage City
            Route::post('city/list' , [ManageCityController::class, 'list']);
            Route::post('city/add' , [ManageCityController::class, 'add']);
            Route::post('city/view' , [ManageCityController::class, 'view']);
            Route::post('city/update' , [ManageCityController::class, 'update']);
            Route::post('city/change-status' , [ManageCityController::class, 'changeStatus']);
            Route::post('city/delete' , [ManageCityController::class, 'delete']);

            // Manage Contact Status
            Route::post('contact-status/list' , [ManageContactStatusController::class, 'list']);
            Route::post('contact-status/add' , [ManageContactStatusController::class, 'add']);
            Route::post('contact-status/view' , [ManageContactStatusController::class, 'view']);
            Route::post('contact-status/update' , [ManageContactStatusController::class, 'update']);
            Route::post('contact-status/change-status' , [ManageContactStatusController::class, 'changeStatus']);
            Route::post('contact-status/delete' , [ManageContactStatusController::class, 'delete']);

            // Manage Task Status
            Route::post('task-status/list' , [ManageTaskStatusController::class, 'list']);
            Route::post('task-status/add' , [ManageTaskStatusController::class, 'add']);
            Route::post('task-status/view' , [ManageTaskStatusController::class, 'view']);
            Route::post('task-status/update' , [ManageTaskStatusController::class, 'update']);
            Route::post('task-status/change-status' , [ManageTaskStatusController::class, 'changeStatus']);
            Route::post('task-status/delete' , [ManageTaskStatusController::class, 'delete']);

            // Manage Designation
            Route::post('designation/list' , [ManageDesignationController::class, 'list']);
            Route::post('designation/add' , [ManageDesignationController::class, 'add']);
            Route::post('designation/view' , [ManageDesignationController::class, 'view']);
            Route::post('designation/update' , [ManageDesignationController::class, 'update']);
            Route::post('designation/change-status' , [ManageDesignationController::class, 'changeStatus']);
            Route::post('designation/delete' , [ManageDesignationController::class, 'delete']);

            // Manage Lead Type
            Route::post('lead-type/list' , [ManageLeadTypeController::class, 'list']);
            Route::post('lead-type/add' , [ManageLeadTypeController::class, 'add']);
            Route::post('lead-type/view' , [ManageLeadTypeController::class, 'view']);
            Route::post('lead-type/update' , [ManageLeadTypeController::class, 'update']);
            Route::post('lead-type/change-status' , [ManageLeadTypeController::class, 'changeStatus']);
            Route::post('lead-type/delete' , [ManageLeadTypeController::class, 'delete']);
        });
    });
});
