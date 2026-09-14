<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DangNhapController;

Route::get('/dang-nhap', [DangNhapController::class, 'dangNhap'])->name('DangNhapController.dangNhap');
Route::get('/dang-nhap/callback', [DangNhapController::class, 'callback'])->name('DangNhapController.callback');
Route::get('/sso/callback', [DangNhapController::class, 'callback']);
Route::post('/dang-xuat', [DangNhapController::class, 'dangXuat'])->name('DangNhapController.dangXuat');

Route::middleware('isLogin')->group(function () {
    Route::view('/{any?}', 'app')->where('any', '^(?!api(?:/|$)).*$');
});
