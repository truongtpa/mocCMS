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

        // 1. Fetch user permissions dynamically from DB
        // Check if user is admin
        $isAdmin = DB::table('vai_tro_nguoi_dung')
            ->join('vai_tro', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro.id')
            ->where('vai_tro_nguoi_dung.user_id', $userId)
            ->where('vai_tro_nguoi_dung.user_type', $userType)
            ->where('vai_tro.ma_vai_tro', 'admin')
            ->exists();

        if ($isAdmin) {
            // Admin gets all permissions
            $permissions = DB::table('quyen_han')->get();
        } else {
            // Normal user gets mapped permissions
            $permissions = DB::table('vai_tro_nguoi_dung')
                ->join('vai_tro_quyen', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro_quyen.vai_tro_id')
                ->join('quyen_han', 'vai_tro_quyen.quyen_id', '=', 'quyen_han.id')
                ->where('vai_tro_nguoi_dung.user_id', $userId)
                ->where('vai_tro_nguoi_dung.user_type', $userType)
                ->select('quyen_han.*')
                ->distinct()
                ->get();
        }

        // Format list of permissions (Controller.method)
        $ds_quyen = $permissions->pluck('ma_quyen')->toArray();

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
            $gv = DB::table('giang_vien')->where('id_giang_vien', $userId)->first();
            if (!$gv) {
                $gv = DB::table('giang_vien')->where('email', $email)->first();
            }
            if ($gv) {
                session()->put(\App\VLUTE::SESSION_HoTen, $gv->ho_ten);
            }
            $info = [
                'id_giang_vien' => $gv ? $gv->id_giang_vien : $userId,
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
    Route::post('/admin/dynamic-objects/types', [\App\Http\Controllers\DynamicObjectController::class, 'saveType'])->name('DynamicObjectController.saveType');
    Route::delete('/admin/dynamic-objects/types/{id}', [\App\Http\Controllers\DynamicObjectController::class, 'deleteType'])->name('DynamicObjectController.deleteType');

    Route::get('/admin/dynamic-objects/fields', [\App\Http\Controllers\DynamicObjectController::class, 'getFields'])->name('DynamicObjectController.getFields');
    Route::post('/admin/dynamic-objects/fields', [\App\Http\Controllers\DynamicObjectController::class, 'saveField'])->name('DynamicObjectController.saveField');
    Route::post('/admin/dynamic-objects/fields/reorder', [\App\Http\Controllers\DynamicObjectController::class, 'reorderFields'])->name('DynamicObjectController.reorderFields');
    Route::delete('/admin/dynamic-objects/fields/{id}', [\App\Http\Controllers\DynamicObjectController::class, 'deleteField'])->name('DynamicObjectController.deleteField');

    Route::get('/admin/dynamic-objects/records', [\App\Http\Controllers\DynamicObjectController::class, 'getRecords'])->name('DynamicObjectController.getRecords');
    Route::post('/admin/dynamic-objects/records', [\App\Http\Controllers\DynamicObjectController::class, 'saveRecord'])->name('DynamicObjectController.saveRecord');
    Route::delete('/admin/dynamic-objects/records/{id}', [\App\Http\Controllers\DynamicObjectController::class, 'deleteRecord'])->name('DynamicObjectController.deleteRecord');

    // RBAC Permission Management API Routes
    Route::get('/admin/phan-quyen/vai-tro', [\App\Http\Controllers\PhanQuyenController::class, 'getDanhSachVaiTro'])->name('PhanQuyenController.getDanhSachVaiTro');
    Route::post('/admin/phan-quyen/vai-tro', [\App\Http\Controllers\PhanQuyenController::class, 'luuVaiTro'])->name('PhanQuyenController.luuVaiTro');
    Route::delete('/admin/phan-quyen/vai-tro/{id}', [\App\Http\Controllers\PhanQuyenController::class, 'xoaVaiTro'])->name('PhanQuyenController.xoaVaiTro');

    Route::get('/admin/phan-quyen/quyen-han', [\App\Http\Controllers\PhanQuyenController::class, 'getDanhSachQuyen'])->name('PhanQuyenController.getDanhSachQuyen');
    Route::post('/admin/phan-quyen/quyen-han', [\App\Http\Controllers\PhanQuyenController::class, 'luuQuyen'])->name('PhanQuyenController.luuQuyen');
    Route::delete('/admin/phan-quyen/quyen-han/{id}', [\App\Http\Controllers\PhanQuyenController::class, 'xoaQuyen'])->name('PhanQuyenController.xoaQuyen');

    Route::get('/admin/phan-quyen/ma-tran', [\App\Http\Controllers\PhanQuyenController::class, 'getMaTranQuyen'])->name('PhanQuyenController.getMaTranQuyen');
    Route::post('/admin/phan-quyen/cap-nhat-quyen-vai-tro', [\App\Http\Controllers\PhanQuyenController::class, 'capNhatQuyenVaiTro'])->name('PhanQuyenController.capNhatQuyenVaiTro');

    Route::get('/admin/phan-quyen/nguoi-dung', [\App\Http\Controllers\PhanQuyenController::class, 'getDanhSachNguoiDung'])->name('PhanQuyenController.getDanhSachNguoiDung');
    Route::post('/admin/phan-quyen/gan-vai-tro-nguoi-dung', [\App\Http\Controllers\PhanQuyenController::class, 'ganVaiTroNguoiDung'])->name('PhanQuyenController.ganVaiTroNguoiDung');
});

// Root route redirect to /admin
Route::get('/', function() {
    return redirect('/admin');
});

// Vue SPA Entry Route
Route::group(['middleware' => ['isLogin']], function () {
    Route::get("/admin/{any?}", [DangNhapController::class, 'trangChu'])->where("any", ".*");
});
