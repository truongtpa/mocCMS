<?php

use App\Http\Controllers\BaiVietController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/admin', 'middleware' => ['web', 'isLogin']], function () {
    Route::get('/bai-viet', [BaiVietController::class, 'getBaiViet'])->name('BaiVietController.getBaiViet');
});
