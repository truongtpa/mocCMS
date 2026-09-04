<?php

use App\Http\Controllers\DynamicObjectController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\PhanQuyenController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/admin', 'middleware' => ['isQuyen']], function () {

    Route::group(['prefix' => '/doi-tuong-dong'], function () {
        Route::get('/types', [DynamicObjectController::class, 'getTypes'])->name('DynamicObjectController.getTypes');
        Route::post('/types', [DynamicObjectController::class, 'saveType'])->name('DynamicObjectController.saveType');
        Route::delete('/types/{id?}', [DynamicObjectController::class, 'deleteType'])->name('DynamicObjectController.deleteType');

        Route::get('/fields', [DynamicObjectController::class, 'getFields'])->name('DynamicObjectController.getFields');
        Route::post('/fields', [DynamicObjectController::class, 'saveField'])->name('DynamicObjectController.saveField');
        Route::post('/fields/reorder', [DynamicObjectController::class, 'reorderFields'])->name('DynamicObjectController.reorderFields');
        Route::delete('/fields/{id?}', [DynamicObjectController::class, 'deleteField'])->name('DynamicObjectController.deleteField');

        Route::get('/records', [DynamicObjectController::class, 'getRecords'])->name('DynamicObjectController.getRecords');
        Route::post('/records', [DynamicObjectController::class, 'saveRecord'])->name('DynamicObjectController.saveRecord');
        Route::delete('/records/{id?}', [DynamicObjectController::class, 'deleteRecord'])->name('DynamicObjectController.deleteRecord');
    });

    Route::group(['prefix' => '/student-portal'], function () {
        Route::get('/dashboard/stats', [StudentPortalController::class, 'getDashboardStats'])->name('StudentPortalController.getDashboardStats');
        Route::post('/booking/update-status', [StudentPortalController::class, 'updateBookingStatus'])->name('StudentPortalController.updateBookingStatus');

        Route::get('/profile/data', [StudentPortalController::class, 'getProfileData'])->name('StudentPortalController.getProfileData');
        Route::post('/profile/update', [StudentPortalController::class, 'updateProfileData'])->name('StudentPortalController.updateProfileData');
        Route::post('/profile/add-attribute', [StudentPortalController::class, 'addEavAttribute'])->name('StudentPortalController.addEavAttribute');
        Route::post('/profile/sync-api', [StudentPortalController::class, 'forceSyncStudentApi'])->name('StudentPortalController.forceSyncStudentApi');

        Route::get('/achievements/list', [StudentPortalController::class, 'getAchievementsList'])->name('StudentPortalController.getAchievementsList');
        Route::post('/achievements/add', [StudentPortalController::class, 'addAchievement'])->name('StudentPortalController.addAchievement');

        Route::get('/booking/lecturers', [StudentPortalController::class, 'getBookingLecturers'])->name('StudentPortalController.getBookingLecturers');
        Route::get('/booking/list', [StudentPortalController::class, 'getBookingsList'])->name('StudentPortalController.getBookingsList');
        Route::post('/booking/create', [StudentPortalController::class, 'createBooking'])->name('StudentPortalController.createBooking');
    });

    Route::group(['prefix' => '/api-key'], function () {
        Route::get('/', [\App\Http\Controllers\ApiKeyController::class, 'getDsApiKey'])->name('ApiKeyController.getDsApiKey');
        Route::post('/', [\App\Http\Controllers\ApiKeyController::class, 'putApiKey'])->name('ApiKeyController.putApiKey');
        Route::delete('/{id}', [\App\Http\Controllers\ApiKeyController::class, 'deleteApiKey'])->name('ApiKeyController.deleteApiKey');
    });
});

Route::get('/sinh-vien', [\App\Http\Controllers\ApiKeyController::class, 'getThongTinSinhVien'])->name('ApiKeyController.getThongTinSinhVien');

