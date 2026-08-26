<?php

use App\Http\Controllers\QuyenController;
use App\Http\Controllers\QuyenNhomController;
use App\Http\Controllers\NhatKyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/admin', 'middleware' => ['isQuyen']], function () {

    Route::group(['prefix' => '/quyen'], function () {
        Route::get('/', [QuyenController::class, 'getQuyen'])->name('QuyenController.getQuyen');
        Route::post('/', [QuyenController::class, 'putQuyen'])->name('QuyenController.putQuyen');
        Route::put('/', [QuyenController::class, 'updateQuyen'])->name('QuyenController.updateQuyen');
        Route::delete('/', [QuyenController::class, 'deleteQuyen'])->name('QuyenController.deleteQuyen');

        Route::get('/chi-tiet', [QuyenController::class, 'getQuyenCT'])->name('QuyenController.getQuyenCT');
        Route::post('/chi-tiet', [QuyenController::class, 'putQuyenCT'])->name('QuyenController.putQuyenCT');
        Route::put('/chi-tiet', [QuyenController::class, 'updateQuyenCT'])->name('QuyenController.updateQuyenCT');
        Route::delete('/chi-tiet', [QuyenController::class, 'deleteQuyenCT'])->name('QuyenController.deleteQuyenCT');
    });

    Route::group(['prefix' => '/quyen-nhom'], function () {
        Route::get('/', [QuyenNhomController::class, 'getQuyenNhom'])->name('QuyenNhomController.getQuyenNhom');
        Route::post('/', [QuyenNhomController::class, 'putQuyenNhom'])->name('QuyenNhomController.putQuyenNhom');
        Route::put('/', [QuyenNhomController::class, 'updateQuyenNhom'])->name('QuyenNhomController.updateQuyenNhom');
        Route::delete('/', [QuyenNhomController::class, 'deleteQuyenNhom'])->name('QuyenNhomController.deleteQuyenNhom');

        Route::get('/ds-quyen-chi-tiet', [QuyenNhomController::class, 'getDsQuyenChiTiet'])->name('QuyenNhomController.getDsQuyenChiTiet');
        Route::get('/chi-tiet', [QuyenNhomController::class, 'getQuyenNhomCT'])->name('QuyenNhomController.getQuyenNhomCT');
        Route::post('/chi-tiet/dong-bo', [QuyenNhomController::class, 'dongBoQuyenNhomCT'])->name('QuyenNhomController.dongBoQuyenNhomCT');
        Route::post('/chi-tiet', [QuyenNhomController::class, 'putQuyenNhomCT'])->name('QuyenNhomController.putQuyenNhomCT');
        Route::put('/chi-tiet', [QuyenNhomController::class, 'updateQuyenNhomCT'])->name('QuyenNhomController.updateQuyenNhomCT');
        Route::delete('/chi-tiet', [QuyenNhomController::class, 'deleteQuyenNhomCT'])->name('QuyenNhomController.deleteQuyenNhomCT');

        Route::get('/tai-khoan', [QuyenNhomController::class, 'getQuyenNhomTK'])->name('QuyenNhomController.getQuyenNhomTK');
        Route::get('/tai-khoan/ds-chon', [QuyenNhomController::class, 'getDsTaiKhoanChon'])->name('QuyenNhomController.getDsTaiKhoanChon');
        Route::post('/tai-khoan', [QuyenNhomController::class, 'putQuyenNhomTK'])->name('QuyenNhomController.putQuyenNhomTK');
        Route::delete('/tai-khoan', [QuyenNhomController::class, 'deleteQuyenNhomTK'])->name('QuyenNhomController.deleteQuyenNhomTK');
    });

    Route::group(['prefix' => '/nhat-ky'], function () {
        Route::get('/', [NhatKyController::class, 'getDanhSach'])->name('NhatKyController.getDanhSach');
        Route::get('/hanh-dong', [NhatKyController::class, 'getDsHanhDong'])->name('NhatKyController.getDsHanhDong');
    });
});
