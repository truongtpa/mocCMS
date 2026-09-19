<?php

use App\Http\Controllers\BaiVietController;
use App\Http\Controllers\QuyenController;
use App\Http\Controllers\QuyenNhomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/admin', 'middleware' => ['web', 'isLogin']], function () {
    Route::get('/bai-viet', [BaiVietController::class, 'getBaiViet'])->name('BaiVietController.getBaiViet');

    Route::group(['prefix' => '/quyen'], function () {
        Route::get('/', [QuyenController::class, 'getQuyen'])->name('QuyenController.getQuyen');
        Route::post('/', [QuyenController::class, 'putQuyen'])->name('QuyenController.putQuyen');
        Route::put('/{id_quyen}', [QuyenController::class, 'updateQuyen'])->name('QuyenController.updateQuyen');
        Route::delete('/{id_quyen}', [QuyenController::class, 'deleteQuyen'])->name('QuyenController.deleteQuyen');

        Route::get('/{id_quyen}/chi-tiet', [QuyenController::class, 'getQuyenCT'])->name('QuyenController.getQuyenCT');
        Route::post('/{id_quyen}/chi-tiet', [QuyenController::class, 'putQuyenCT'])->name('QuyenController.putQuyenCT');
        Route::put('/{id_quyen}/chi-tiet/{id_quyen_chi_tiet}', [QuyenController::class, 'updateQuyenCT'])->name('QuyenController.updateQuyenCT');
        Route::delete('/{id_quyen}/chi-tiet/{id_quyen_chi_tiet}', [QuyenController::class, 'deleteQuyenCT'])->name('QuyenController.deleteQuyenCT');
    });

    Route::group(['prefix' => '/quyen-nhom'], function () {
        Route::get('/', [QuyenNhomController::class, 'getQuyenNhom'])->name('QuyenNhomController.getQuyenNhom');
        Route::post('/', [QuyenNhomController::class, 'putQuyenNhom'])->name('QuyenNhomController.putQuyenNhom');
        Route::put('/{id_quyen_nhom}', [QuyenNhomController::class, 'updateQuyenNhom'])->name('QuyenNhomController.updateQuyenNhom');
        Route::delete('/{id_quyen_nhom}', [QuyenNhomController::class, 'deleteQuyenNhom'])->name('QuyenNhomController.deleteQuyenNhom');

        Route::get('/{id_quyen_nhom}/chi-tiet', [QuyenNhomController::class, 'getQuyenNhomCT'])->name('QuyenNhomController.getQuyenNhomCT');
        Route::post('/{id_quyen_nhom}/chi-tiet', [QuyenNhomController::class, 'putQuyenNhomCT'])->name('QuyenNhomController.putQuyenNhomCT');
        Route::delete('/{id_quyen_nhom}/chi-tiet/{id_quyen_nhom_chi_tiet}', [QuyenNhomController::class, 'deleteQuyenNhomCT'])->name('QuyenNhomController.deleteQuyenNhomCT');

        Route::get('/{id_quyen_nhom}/ds-quyen-chi-tiet', [QuyenNhomController::class, 'getDsQuyenChiTiet'])->name('QuyenNhomController.getDsQuyenChiTiet');
        Route::put('/{id_quyen_nhom}/ds-quyen-chi-tiet', [QuyenNhomController::class, 'updateDsQuyenChiTiet'])->name('QuyenNhomController.updateDsQuyenChiTiet');

        Route::get('/{id_quyen_nhom}/tai-khoan', [QuyenNhomController::class, 'getQuyenNhomTK'])->name('QuyenNhomController.getQuyenNhomTK');
        Route::post('/{id_quyen_nhom}/tai-khoan', [QuyenNhomController::class, 'putQuyenNhomTK'])->name('QuyenNhomController.putQuyenNhomTK');
        Route::delete('/{id_quyen_nhom}/tai-khoan/{id_quyen_nhom_tai_khoan}', [QuyenNhomController::class, 'deleteQuyenNhomTK'])->name('QuyenNhomController.deleteQuyenNhomTK');

        Route::get('/{id_quyen_nhom}/ds-tai-khoan', [QuyenNhomController::class, 'getDsTaiKhoanChon'])->name('QuyenNhomController.getDsTaiKhoanChon');
    });
});
