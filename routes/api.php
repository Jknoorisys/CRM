<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ManageActivityController;
use App\Http\Controllers\api\ManageContactController;
use App\Http\Controllers\api\ManageLeadController;
use App\Http\Controllers\api\ManageTaskController;
use App\Http\Controllers\api\ManageUserController;
use App\Http\Controllers\api\master\ManageCityController;
use App\Http\Controllers\api\master\ManageContactStatusController;
use App\Http\Controllers\api\master\ManageCountryController;
use App\Http\Controllers\api\master\ManageDesignationController;
use App\Http\Controllers\api\master\ManageLeadTypeController;
use App\Http\Controllers\api\master\ManageMediumController;
use App\Http\Controllers\api\master\ManageReferredByController;
use App\Http\Controllers\api\master\ManageSourceController;
use App\Http\Controllers\api\master\ManageStageController;
use App\Http\Controllers\api\master\ManageTaskStatusController;
use App\Http\Controllers\api\MastersController;
use App\Http\Controllers\api\ProfileController;
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

            // Manage Activity Medium
            Route::post('medium/list' , [ManageMediumController::class, 'list']);
            Route::post('medium/add' , [ManageMediumController::class, 'add']);
            Route::post('medium/view' , [ManageMediumController::class, 'view']);
            Route::post('medium/update' , [ManageMediumController::class, 'update']);
            Route::post('medium/change-status' , [ManageMediumController::class, 'changeStatus']);
            Route::post('medium/delete' , [ManageMediumController::class, 'delete']);

            // Manage Lead Stages
            Route::post('stage/list' , [ManageStageController::class, 'list']);
            Route::post('stage/add' , [ManageStageController::class, 'add']);
            Route::post('stage/view' , [ManageStageController::class, 'view']);
            Route::post('stage/update' , [ManageStageController::class, 'update']);
            Route::post('stage/change-status' , [ManageStageController::class, 'changeStatus']);
            Route::post('stage/delete' , [ManageStageController::class, 'delete']);

            // Manage Lead Sources
            Route::post('source/list' , [ManageSourceController::class, 'list']);
            Route::post('source/add' , [ManageSourceController::class, 'add']);
            Route::post('source/view' , [ManageSourceController::class, 'view']);
            Route::post('source/update' , [ManageSourceController::class, 'update']);
            Route::post('source/change-status' , [ManageSourceController::class, 'changeStatus']);
            Route::post('source/delete' , [ManageSourceController::class, 'delete']);

            // Manage Referred By
            Route::post('referred-by/list' , [ManageReferredByController::class, 'list']);
            Route::post('referred-by/add' , [ManageReferredByController::class, 'add']);
            Route::post('referred-by/view' , [ManageReferredByController::class, 'view']);
            Route::post('referred-by/update' , [ManageReferredByController::class, 'update']);
            Route::post('referred-by/change-status' , [ManageReferredByController::class, 'changeStatus']);
            Route::post('referred-by/delete' , [ManageReferredByController::class, 'delete']);
        });

        // Manage Tasks
        Route::post('task/list' , [ManageTaskController::class, 'list']);
        Route::post('task/add' , [ManageTaskController::class, 'add']);
        Route::post('task/view' , [ManageTaskController::class, 'view']);
        Route::post('task/update' , [ManageTaskController::class, 'update']);
        Route::post('task/change-status' , [ManageTaskController::class, 'changeStatus']);
        Route::post('task/delete' , [ManageTaskController::class, 'delete']);

        // Master APIs for dropdown
        Route::get('master/referred-by' , [MastersController::class, 'referredBy']);
        Route::get('master/country' , [MastersController::class, 'country']);
        Route::get('master/city' , [MastersController::class, 'city']);
        Route::get('master/designation' , [MastersController::class, 'designation']);
        Route::get('master/contact-status' , [MastersController::class, 'contactStatus']);
        Route::get('master/lead-stage' , [MastersController::class, 'leadStage']);
        Route::get('master/lead-type' , [MastersController::class, 'leadType']);
        Route::get('master/lead-source' , [MastersController::class, 'leadSource']);
        Route::get('master/activity-medium' , [MastersController::class, 'activityMedium']);
        Route::get('master/task-status' , [MastersController::class, 'taskStatus']);
        Route::get('master/users' , [MastersController::class, 'users']);

        // Manage Contacts
        Route::post('contact/list' , [ManageContactController::class, 'list']);
        Route::post('contact/add' , [ManageContactController::class, 'add']);
        Route::post('contact/view' , [ManageContactController::class, 'view']);
        Route::post('contact/update' , [ManageContactController::class, 'update']);
        Route::post('contact/change-status' , [ManageContactController::class, 'changeStatus']);
        Route::post('contact/delete' , [ManageContactController::class, 'delete']);

        // Manage Leads
        Route::post('lead/list' , [ManageLeadController::class, 'list']);
        Route::post('lead/add' , [ManageLeadController::class, 'add']);
        Route::post('lead/view' , [ManageLeadController::class, 'view']);
        Route::post('lead/update' , [ManageLeadController::class, 'update']);
        Route::post('lead/change-status' , [ManageLeadController::class, 'changeStatus']);
        Route::post('lead/delete' , [ManageLeadController::class, 'delete']);

        // Manage Activities
        Route::post('activity/list' , [ManageActivityController::class, 'list']);
        Route::post('activity/add' , [ManageActivityController::class, 'add']);
        Route::post('activity/view' , [ManageActivityController::class, 'view']);
        Route::post('activity/update' , [ManageActivityController::class, 'update']);
        Route::post('activity/change-status' , [ManageActivityController::class, 'changeStatus']);
        Route::post('activity/delete' , [ManageActivityController::class, 'delete']);
    });
});
