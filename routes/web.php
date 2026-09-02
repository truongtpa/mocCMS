<?php

use App\Http\Controllers\DangNhapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Keycloak SSO Login/Logout routes
Route::get('/admin/sso', [DangNhapController::class, 'dangNhapKeycloak']);
Route::get('/admin/sso/callback', [DangNhapController::class, 'callbackKeycloak']);
Route::get('/sso/callback', [DangNhapController::class, 'callbackKeycloak']);
Route::get('/admin/sso/logout', [DangNhapController::class, 'dangXuat'])->name('dangXuat');
Route::get('/admin/sso/change-password', [DangNhapController::class, 'thayDoiMatKhau'])->name('changePassword');
Route::get("/admin/empty-permission", [DangNhapController::class, 'redirectKhongCoQuyen'])->name('redirectKhongCoQuyen');

Route::group(['middleware' => ['isLogin']], function () {
    Route::get("/admin/generate-token", [DangNhapController::class, 'generateToken'])->name('generateToken');

    Route::get('/admin/tai-khoan/quyen-chi-tiet', function() {
        $userId = session()->get(\App\VLUTE::SESSION_IDTaiKhoan);
        $email = session()->get(\App\VLUTE::SESSION_Email);

        if (!$userId || !$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }

        $maDoiTuong = explode('@', $email)[0];
        $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
        $userType = $isStudent ? 'sinh_vien' : 'giang_vien';

        $permData = \App\VLUTE::getUserPermissions($userId);
        $ds_quyen = array_values(array_unique(array_merge($permData['funcs'], $permData['show_views'])));
        $isAdmin = $permData['is_admin'];

        // 2. Fetch user profile info
        if ($isStudent) {
            $sv = DB::table('sinh_vien')->where('id', $userId)->first();
            if (!$sv) {
                $sv = DB::table('sinh_vien')->where('email', $email)->first();
            }
            if ($sv) {
                session()->put(\App\VLUTE::SESSION_HoTen, $sv->ho_ten);
            }
            $info = [
                'id_sinh_vien' => $sv ? $sv->id : $userId,
                'ho_ten' => $sv ? $sv->ho_ten : session()->get(\App\VLUTE::SESSION_HoTen, 'Sinh viên'),
                'email' => $email,
                'mssv' => $maDoiTuong,
                'user_type' => 'sinh_vien',
                'is_admin' => $isAdmin
            ];
        } else {
            $gv = DB::table('giang_vien')->where('id', $userId)->first();
            if (!$gv) {
                $gv = DB::table('giang_vien')->where('email', $email)->first();
            }
            if ($gv) {
                session()->put(\App\VLUTE::SESSION_HoTen, $gv->ho_ten);
            }
            $info = [
                'id' => $gv ? $gv->id : $userId,
                'ho_ten' => $gv ? $gv->ho_ten : session()->get(\App\VLUTE::SESSION_HoTen, 'Giảng viên'),
                'email' => $email,
                'user_type' => 'giang_vien',
                'is_admin' => $isAdmin
            ];
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'info' => $info,
                'ds_quyen' => $ds_quyen
            ]
        ]);
    })->name('TaiKhoanController.taiKhoanChiTiet');

    Route::group(['middleware' => ['isQuyen']], function () {
        // Student Portal Feature Endpoints
        Route::get('/admin/dashboard/stats', [\App\Http\Controllers\StudentPortalController::class, 'getDashboardStats'])->name('StudentPortalController.getDashboardStats');
        Route::post('/admin/booking/update-status', [\App\Http\Controllers\StudentPortalController::class, 'updateBookingStatus'])->name('StudentPortalController.updateBookingStatus');

        Route::get('/admin/profile/data', [\App\Http\Controllers\StudentPortalController::class, 'getProfileData'])->name('StudentPortalController.getProfileData');
        Route::post('/admin/profile/update', [\App\Http\Controllers\StudentPortalController::class, 'updateProfileData'])->name('StudentPortalController.updateProfileData');
        Route::post('/admin/profile/add-attribute', [\App\Http\Controllers\StudentPortalController::class, 'addEavAttribute'])->name('StudentPortalController.addEavAttribute');
        Route::post('/admin/profile/sync-api', [\App\Http\Controllers\StudentPortalController::class, 'forceSyncStudentApi'])->name('StudentPortalController.forceSyncStudentApi');
        
        Route::get('/admin/achievements/list', [\App\Http\Controllers\StudentPortalController::class, 'getAchievementsList'])->name('StudentPortalController.getAchievementsList');
        Route::post('/admin/achievements/add', [\App\Http\Controllers\StudentPortalController::class, 'addAchievement'])->name('StudentPortalController.addAchievement');
        
        Route::get('/admin/booking/lecturers', [\App\Http\Controllers\StudentPortalController::class, 'getBookingLecturers'])->name('StudentPortalController.getBookingLecturers');
        Route::get('/admin/booking/list', [\App\Http\Controllers\StudentPortalController::class, 'getBookingsList'])->name('StudentPortalController.getBookingsList');
        Route::post('/admin/booking/create', [\App\Http\Controllers\StudentPortalController::class, 'createBooking'])->name('StudentPortalController.createBooking');

        // Dynamic Objects API Routes
        Route::get('/admin/dynamic-objects/types', [\App\Http\Controllers\DynamicObjectController::class, 'getTypes'])->name('DynamicObjectController.getTypes');
        Route::post('/admin/dynamic-objects/types', [\App\Http\Controllers\DynamicObjectController::class, 'putType'])->name('DynamicObjectController.putType');
        Route::delete('/admin/dynamic-objects/types/{id}', [\App\Http\Controllers\DynamicObjectController::class, 'deleteType'])->name('DynamicObjectController.deleteType');

        Route::get('/admin/dynamic-objects/fields', [\App\Http\Controllers\DynamicObjectController::class, 'getFields'])->name('DynamicObjectController.getFields');
        Route::post('/admin/dynamic-objects/fields', [\App\Http\Controllers\DynamicObjectController::class, 'putField'])->name('DynamicObjectController.putField');
        Route::post('/admin/dynamic-objects/fields/reorder', [\App\Http\Controllers\DynamicObjectController::class, 'updateFieldOrders'])->name('DynamicObjectController.updateFieldOrders');
        Route::delete('/admin/dynamic-objects/fields/{id}', [\App\Http\Controllers\DynamicObjectController::class, 'deleteField'])->name('DynamicObjectController.deleteField');

        Route::get('/admin/dynamic-objects/records', [\App\Http\Controllers\DynamicObjectController::class, 'getRecords'])->name('DynamicObjectController.getRecords');
        Route::post('/admin/dynamic-objects/records', [\App\Http\Controllers\DynamicObjectController::class, 'putRecord'])->name('DynamicObjectController.putRecord');
        Route::delete('/admin/dynamic-objects/records/{id}', [\App\Http\Controllers\DynamicObjectController::class, 'deleteRecord'])->name('DynamicObjectController.deleteRecord');

        Route::get('/admin/dynamic-objects/import/template', [\App\Http\Controllers\DynamicObjectController::class, 'exportImportTemplate'])->name('DynamicObjectController.exportImportTemplate');
        Route::post('/admin/dynamic-objects/import/preview', [\App\Http\Controllers\DynamicObjectController::class, 'putPreviewImport'])->name('DynamicObjectController.putPreviewImport');
        Route::post('/admin/dynamic-objects/import/process', [\App\Http\Controllers\DynamicObjectController::class, 'putProcessImport'])->name('DynamicObjectController.putProcessImport');
        Route::get('/admin/dynamic-objects/layout-config', [\App\Http\Controllers\DynamicObjectController::class, 'getLayoutConfig'])->name('DynamicObjectController.getLayoutConfig');
        Route::post('/admin/dynamic-objects/layout-config', [\App\Http\Controllers\DynamicObjectController::class, 'putLayoutConfig'])->name('DynamicObjectController.putLayoutConfig');
        Route::get('/admin/dynamic-objects/export/records', [\App\Http\Controllers\DynamicObjectController::class, 'exportRecords'])->name('DynamicObjectController.exportRecords');

        // RBAC Permission Management API Routes
        Route::get('/admin/phan-quyen/vai-tro', [\App\Http\Controllers\PhanQuyenController::class, 'getDanhSachVaiTro'])->name('PhanQuyenController.getDanhSachVaiTro');
        Route::post('/admin/phan-quyen/vai-tro', [\App\Http\Controllers\PhanQuyenController::class, 'putVaiTro'])->name('PhanQuyenController.putVaiTro');
        Route::delete('/admin/phan-quyen/vai-tro/{id}', [\App\Http\Controllers\PhanQuyenController::class, 'deleteVaiTro'])->name('PhanQuyenController.deleteVaiTro');

        Route::get('/admin/phan-quyen/quyen-han', [\App\Http\Controllers\PhanQuyenController::class, 'getDanhSachQuyen'])->name('PhanQuyenController.getDanhSachQuyen');
        Route::post('/admin/phan-quyen/quyen-han', [\App\Http\Controllers\PhanQuyenController::class, 'putQuyen'])->name('PhanQuyenController.putQuyen');
        Route::delete('/admin/phan-quyen/quyen-han/{id}', [\App\Http\Controllers\PhanQuyenController::class, 'deleteQuyen'])->name('PhanQuyenController.deleteQuyen');

        Route::get('/admin/phan-quyen/ma-tran', [\App\Http\Controllers\PhanQuyenController::class, 'getMaTranQuyen'])->name('PhanQuyenController.getMaTranQuyen');
        Route::post('/admin/phan-quyen/cap-nhat-quyen-vai-tro', [\App\Http\Controllers\PhanQuyenController::class, 'updateQuyenVaiTro'])->name('PhanQuyenController.updateQuyenVaiTro');

        Route::get('/admin/phan-quyen/nguoi-dung', [\App\Http\Controllers\PhanQuyenController::class, 'getDanhSachNguoiDung'])->name('PhanQuyenController.getDanhSachNguoiDung');
        Route::post('/admin/phan-quyen/gan-vai-tro-nguoi-dung', [\App\Http\Controllers\PhanQuyenController::class, 'putVaiTroNguoiDung'])->name('PhanQuyenController.putVaiTroNguoiDung');

        Route::get('/admin/phan-quyen/cai-dat', [\App\Http\Controllers\PhanQuyenController::class, 'getCaiDat'])->name('PhanQuyenController.getCaiDat');
        Route::post('/admin/phan-quyen/cai-dat', [\App\Http\Controllers\PhanQuyenController::class, 'putCaiDat'])->name('PhanQuyenController.putCaiDat');
        Route::delete('/admin/phan-quyen/cai-dat/{id}', [\App\Http\Controllers\PhanQuyenController::class, 'deleteCaiDat'])->name('PhanQuyenController.deleteCaiDat');
    });
});

// Root route redirect to /admin
Route::get('/', function() {
    return redirect('/admin');
});

// Vue SPA Entry Route
Route::group(['middleware' => ['isLogin']], function () {
    Route::get("/admin/{any?}", [DangNhapController::class, 'trangChu'])->where("any", ".*");
});
