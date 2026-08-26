<?php

use App\Http\Controllers\DangNhapController;
use Illuminate\Support\Facades\Route;

Route::get('/sso', [DangNhapController::class, 'dangNhapKeycloak']);
Route::get('/sso/callback', [DangNhapController::class, 'callbackKeycloak']);
Route::get('/sso/logout', [DangNhapController::class, 'dangXuat'])->name('dangXuat');
Route::get('/sso/change-password', [DangNhapController::class, 'thayDoiMatKhau'])->name('changePassword');

Route::group(['middleware' => ['isLogin']], function () {
    Route::get('{any}', [DangNhapController::class, 'trangChu'])->where("any", ".*");
});
