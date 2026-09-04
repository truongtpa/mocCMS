<?php

namespace App\Http\Controllers;

use App\Services\S3Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentPortalController extends Controller
{
    // ==========================================
    // PROFILE ENDPOINTS
    // ==========================================
    
    public function getProfileData(Request $r)
    {
        $userId = $r->session()->get(\App\VLUTE::SESSION_IDTaiKhoan);
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);

        if (!$userId || !$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }

        $maDoiTuong = explode('@', $email)[0];
        $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');

        if ($isStudent) {
            $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
            if (!$sv) {
                DB::table('sinh_vien')->insert([
                    'mssv' => $maDoiTuong,
                    'ho_ten' => $r->session()->get(\App\VLUTE::SESSION_HoTen, 'Sinh viên ' . $maDoiTuong),
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
            }

            $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'sinh_vien')->first();
            $attributes = collect();

            if ($loaiDoiTuong) {
                $masterDoiTuongId = self::getMasterDoiTuongId($loaiDoiTuong->id, $sv->mssv, $sv->ho_ten);
                $possibleIds = array_unique([$masterDoiTuongId, $sv->id]);

                // Auto sync from Daotao API if no dynamic values exist yet for student
                $existingValCount = DB::table('gia_tri_thong_tin')
                    ->whereIn('doi_tuong_id', $possibleIds)
                    ->count();
                if ($existingValCount === 0) {
                    self::syncStudentDataFromApi($maDoiTuong, $email, $masterDoiTuongId);
                }

                $attributes = DB::table('danh_muc_truong')
                    ->where('loai_doi_tuong_id', $loaiDoiTuong->id)
                    ->where('trang_thai', true)
                    ->orderBy('thu_tu', 'asc')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(function ($attr) use ($possibleIds) {
                        $val = DB::table('gia_tri_thong_tin')
                            ->whereIn('doi_tuong_id', $possibleIds)
                            ->where('truong_id', $attr->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        $rawVal = $val ? $val->gia_tri : '';
                        if (in_array($attr->kieu_du_lieu, ['image', 'file']) && !empty($rawVal)) {
                            $attr->value = \App\Services\S3Services::urlCongKhai($rawVal);
                        } else {
                            $attr->value = $rawVal;
                        }
                        $attr->cho_phep_chinh_sua = (bool)($attr->cho_phep_chinh_sua ?? true);
                        
                        $cauHinh = [];
                        if ($attr->cau_hinh) {
                            try {
                                $cauHinh = is_string($attr->cau_hinh) ? json_decode($attr->cau_hinh, true) : (array)$attr->cau_hinh;
                            } catch (\Exception $e) {}
                        }
                        $defaultColSpan = in_array($attr->kieu_du_lieu, ['textarea', 'file', 'image']) ? 12 : 6;
                        $attr->col_span = intval($cauHinh['col_span'] ?? $defaultColSpan);

                        $refOptions = \App\Http\Controllers\DynamicObjectController::resolveAttributeOptions($attr);
                        if ($refOptions !== null) {
                            $attr->ref_options = $refOptions;
                        }
                        return $attr;
                    });
            }

            $profileData = (object) array_merge((array)$sv, ['user_type' => 'sinh_vien']);

            return response()->json([
                'status' => 200,
                'data' => [
                    'profile' => $profileData,
                    'attributes' => $attributes
                ]
            ]);
        } else {
            // Lecturer profile
            $gv = DB::table('giang_vien')->where('email', $email)->first();
            if (!$gv) {
                DB::table('giang_vien')->insert([
                    'ho_ten' => $r->session()->get(\App\VLUTE::SESSION_HoTen, 'Giảng viên'),
                    'email' => $email,
                    'id_don_vi' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $gv = DB::table('giang_vien')->where('email', $email)->first();
            }

            $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'giang_vien')->first();
            $attributes = collect();

            if ($loaiDoiTuong) {
                $msgv = explode('@', $gv->email)[0];
                $masterDoiTuongId = self::getMasterDoiTuongId($loaiDoiTuong->id, $msgv, $gv->ho_ten);
                $possibleIds = array_unique([$masterDoiTuongId, $gv->id]);

                $attributes = DB::table('danh_muc_truong')
                    ->where('loai_doi_tuong_id', $loaiDoiTuong->id)
                    ->where('trang_thai', true)
                    ->orderBy('thu_tu', 'asc')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(function ($attr) use ($possibleIds) {
                        $val = DB::table('gia_tri_thong_tin')
                            ->whereIn('doi_tuong_id', $possibleIds)
                            ->where('truong_id', $attr->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        $rawVal = $val ? $val->gia_tri : '';
                        if (in_array($attr->kieu_du_lieu, ['image', 'file']) && !empty($rawVal)) {
                            $attr->value = \App\Services\S3Services::urlCongKhai($rawVal);
                        } else {
                            $attr->value = $rawVal;
                        }
                        $attr->cho_phep_chinh_sua = (bool)($attr->cho_phep_chinh_sua ?? true);
                        
                        $cauHinh = [];
                        if ($attr->cau_hinh) {
                            try {
                                $cauHinh = is_string($attr->cau_hinh) ? json_decode($attr->cau_hinh, true) : (array)$attr->cau_hinh;
                            } catch (\Exception $e) {}
                        }
                        $defaultColSpan = in_array($attr->kieu_du_lieu, ['textarea', 'file', 'image']) ? 12 : 6;
                        $attr->col_span = intval($cauHinh['col_span'] ?? $defaultColSpan);

                        $refOptions = \App\Http\Controllers\DynamicObjectController::resolveAttributeOptions($attr);
                        if ($refOptions !== null) {
                            $attr->ref_options = $refOptions;
                        }
                        return $attr;
                    });
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'profile' => [
                        'id' => $gv->id,
                        'ho_ten' => $gv->ho_ten,
                        'email' => $gv->email,
                        'id_don_vi' => $gv->id_don_vi,
                        'user_type' => 'giang_vien'
                    ],
                    'attributes' => $attributes
                ]
            ]);
        }
    }

    public function updateProfileData(Request $r)
    {
        $userId = $r->session()->get(\App\VLUTE::SESSION_IDTaiKhoan);
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);

        if (!$userId || !$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }

        $maDoiTuong = explode('@', $email)[0];
        $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');

        if ($isStudent) {
            $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
            if (!$sv) {
                return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
            }
            $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'sinh_vien')->first();
            $doiTuongId = $loaiDoiTuong ? self::getMasterDoiTuongId($loaiDoiTuong->id, $sv->mssv, $sv->ho_ten) : $sv->id;
            $maLoai = 'sinh_vien';
        } else {
            $gv = DB::table('giang_vien')->where('email', $email)->first();
            if (!$gv) {
                return response()->json(['status' => 404, 'message' => 'Lecturer not found'], 404);
            }
            $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'giang_vien')->first();
            $msgv = explode('@', $gv->email)[0];
            $doiTuongId = $loaiDoiTuong ? self::getMasterDoiTuongId($loaiDoiTuong->id, $msgv, $gv->ho_ten) : $gv->id;
            $maLoai = 'giang_vien';
        }

        $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', $maLoai)->first();
        if (!$loaiDoiTuong) {
            return response()->json(['status' => 400, 'message' => 'Loại đối tượng chưa được cấu hình'], 400);
        }

        $attrsRaw = $r->input('attributes', []);
        if (is_string($attrsRaw)) {
            $attrsRaw = json_decode($attrsRaw, true) ?? [];
        }

        $oldAttrs = [];
        $newAttrs = [];

        foreach ($attrsRaw as $attr) {
            $truongId = $attr['truong_id'] ?? null;
            if (!$truongId) continue;

            $fieldDef = DB::table('danh_muc_truong')->where('id', $truongId)->first();
            if ($fieldDef && isset($fieldDef->cho_phep_chinh_sua) && !$fieldDef->cho_phep_chinh_sua) {
                continue;
            }

            $val = $attr['gia_tri'] ?? '';
            if (is_string($val) && $fieldDef && in_array($fieldDef->kieu_du_lieu, ['file', 'image'])) {
                if (str_contains($val, '?')) {
                    $val = explode('?', $val)[0];
                }
                $bucket = env('AWS_BUCKET', 'daotao-vlute-edu-vn');
                if (str_contains($val, '/' . $bucket . '/')) {
                    $parts = explode('/' . $bucket . '/', $val);
                    $val = end($parts);
                } elseif (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
                    $parsed = parse_url($val, PHP_URL_PATH);
                    $val = ltrim($parsed ?? '', '/');
                }
                $val = ltrim($val, '/');
            }

            // Handle file upload if present
            if ($r->hasFile("file_{$truongId}")) {
                $uploadedFile = $r->file("file_{$truongId}");
                $val = $this->uploadFileToS3($uploadedFile);
            }

            // Clean up old S3 file if file is being replaced or updated
            $oldRow = DB::table('gia_tri_thong_tin')
                ->where('doi_tuong_id', $doiTuongId)
                ->where('truong_id', $truongId)
                ->orderBy('id', 'desc')
                ->first();

            $attrKey = $fieldDef ? $fieldDef->ma_truong : ('truong_' . $truongId);
            if ($oldRow) {
                $oldAttrs[$attrKey] = $oldRow->gia_tri;
            }

            if ($fieldDef && in_array($fieldDef->kieu_du_lieu, ['file', 'image'])) {
                if ($oldRow && !empty($oldRow->gia_tri) && $oldRow->gia_tri !== $val) {
                    \App\Services\S3Services::xoaFile($oldRow->gia_tri);
                }
            }

            DB::table('gia_tri_thong_tin')->updateOrInsert(
                [
                    'doi_tuong_id' => $doiTuongId,
                    'truong_id' => $truongId
                ],
                [
                    'gia_tri' => $val,
                    'ngay_tao' => now()
                ]
            );

            $newAttrs[$attrKey] = $val;
        }

        \App\Services\NhatKyService::ghiLog(
            'CAP_NHAT_TRANG_CA_NHAN',
            "Tài khoản '{$email}' đã cập nhật thông tin cá nhân",
            'gia_tri_thong_tin',
            $doiTuongId,
            !empty($oldAttrs) ? $oldAttrs : null,
            !empty($newAttrs) ? $newAttrs : null
        );

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật thành công'
        ]);
    }

    private function uploadFileToS3($uploadedFile)
    {
        try {
            $disk = env('FILESYSTEM_DISK', 's3');
            $targetDisk = in_array($disk, ['s3', 'minio']) ? $disk : 's3';

            $path = $uploadedFile->store('dynamic_uploads', $targetDisk);
            if ($path) {
                return $path;
            }
        } catch (\Exception $e) {
            Log::error("S3 Storage Upload Error: " . $e->getMessage());
        }

        $path = $uploadedFile->store('public/uploads');
        return '/storage/' . str_replace('public/', '', $path);
    }

    public function addEavAttribute(Request $r)
    {
        $ma = $r->input('ma_truong');
        $ten = $r->input('ten_truong');
        $kieu = $r->input('kieu_du_lieu', 'text');

        if (!$ma || !$ten) {
            return response()->json(['status' => 400, 'message' => 'Mã và tên trường không được để trống'], 400);
        }

        $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'sinh_vien')->first();
        if (!$loaiDoiTuong) {
            return response()->json(['status' => 400, 'message' => 'Loại đối tượng sinh viên chưa tồn tại'], 400);
        }

        DB::table('danh_muc_truong')->insert([
            'loai_doi_tuong_id' => $loaiDoiTuong->id,
            'ma_truong' => $ma,
            'ten_truong' => $ten,
            'kieu_du_lieu' => $kieu,
            'phan_nhom' => 'Thông tin bổ sung',
            'trang_thai' => true,
            'ngay_tao' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Thêm trường thông tin EAV thành công'
        ]);
    }

    // ==========================================
    // ACHIEVEMENTS ENDPOINTS
    // ==========================================

    public function getAchievementsList(Request $r)
    {
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);
        if (!$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }
        $maDoiTuong = explode('@', $email)[0];
        
        $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
        if (!$sv) {
            return response()->json(['status' => 200, 'data' => []]);
        }

        $data = DB::table('thanh_tich')
            ->where('sinh_vien_id', $sv->id)
            ->orderBy('nam_nhan', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function addAchievement(Request $r)
    {
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);
        if (!$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }
        $maDoiTuong = explode('@', $email)[0];

        $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
        if (!$sv) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        $ten = $r->input('ten_giai_thuong');
        $nam = $r->input('nam_nhan');
        $cap = $r->input('cap_khen_thuong');
        $file = $r->input('file_minh_chung');

        DB::table('thanh_tich')->insert([
            'sinh_vien_id' => $sv->id,
            'ten_giai_thuong' => $ten,
            'nam_nhan' => $nam,
            'cap_khen_thuong' => $cap,
            'file_minh_chung' => $file,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Lưu thành tích thành công'
        ]);
    }

    // ==========================================
    // BOOKING ENDPOINTS
    // ==========================================

    public function getBookingLecturers(Request $r)
    {
        $lecturers = DB::table('giang_vien')->get();
        return response()->json([
            'status' => 200,
            'data' => $lecturers
        ]);
    }

    public function getBookingsList(Request $r)
    {
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);
        if (!$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }
        $maDoiTuong = explode('@', $email)[0];

        $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
        if (!$sv) {
            return response()->json(['status' => 200, 'data' => []]);
        }

        $data = DB::table('lich_hen')
            ->join('giang_vien', 'lich_hen.giang_vien_id', '=', 'giang_vien.id')
            ->where('lich_hen.sinh_vien_id', $sv->id)
            ->select('lich_hen.*', 'giang_vien.ho_ten as ten_giang_vien')
            ->orderBy('lich_hen.thoi_gian_bat_dau', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function createBooking(Request $r)
    {
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);
        if (!$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }
        $maDoiTuong = explode('@', $email)[0];

        $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
        if (!$sv) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        $gvId = $r->input('giang_vien_id');
        $time = $r->input('thoi_gian_bat_dau');
        $content = $r->input('noi_dung');

        DB::table('lich_hen')->insert([
            'sinh_vien_id' => $sv->id,
            'giang_vien_id' => $gvId,
            'thoi_gian_bat_dau' => $time,
            'noi_dung' => $content,
            'trang_thai' => 'cho_duyet',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Đặt lịch thành công'
        ]);
    }

    // ==========================================
    // DASHBOARD ENDPOINTS
    // ==========================================

    public function getDashboardStats(Request $r)
    {
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);
        if (!$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }
        $maDoiTuong = explode('@', $email)[0];
        $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');

        if ($isStudent) {
            $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
            if (!$sv) {
                return response()->json(['status' => 200, 'data' => ['user_type' => 'sinh_vien', 'stats' => ['achievements' => 0, 'bookings' => 0, 'pending' => 0], 'bookings' => []]]);
            }

            $achievementsCount = DB::table('thanh_tich')->where('sinh_vien_id', $sv->id)->count();
            $bookingsCount = DB::table('lich_hen')->where('sinh_vien_id', $sv->id)->count();
            $pendingCount = DB::table('lich_hen')->where('sinh_vien_id', $sv->id)->where('trang_thai', 'cho_duyet')->count();

            $recentBookings = DB::table('lich_hen')
                ->join('giang_vien', 'lich_hen.giang_vien_id', '=', 'giang_vien.id')
                ->where('lich_hen.sinh_vien_id', $sv->id)
                ->select('lich_hen.*', 'giang_vien.ho_ten as ten_giang_vien')
                ->orderBy('lich_hen.thoi_gian_bat_dau', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'status' => 200,
                'data' => [
                    'user_type' => 'sinh_vien',
                    'stats' => [
                        'achievements' => $achievementsCount,
                        'bookings' => $bookingsCount,
                        'pending' => $pendingCount
                    ],
                    'bookings' => $recentBookings
                ]
            ]);
        } else {
            $gv = DB::table('giang_vien')->where('email', $email)->first();
            if (!$gv) {
                return response()->json(['status' => 200, 'data' => ['user_type' => 'giang_vien', 'stats' => ['students' => 0, 'pending' => 0, 'confirmed' => 0], 'bookings' => []]]);
            }

            $studentsCount = DB::table('sinh_vien')->count();
            $pendingCount = DB::table('lich_hen')->where('giang_vien_id', $gv->id)->where('trang_thai', 'cho_duyet')->count();
            $confirmedCount = DB::table('lich_hen')->where('giang_vien_id', $gv->id)->where('trang_thai', 'da_xac_nhan')->count();

            $bookingRequests = DB::table('lich_hen')
                ->join('sinh_vien', 'lich_hen.sinh_vien_id', '=', 'sinh_vien.id')
                ->where('lich_hen.giang_vien_id', $gv->id)
                ->select('lich_hen.*', 'sinh_vien.ho_ten as ten_sinh_vien', 'sinh_vien.mssv')
                ->orderBy('lich_hen.thoi_gian_bat_dau', 'desc')
                ->get();

            return response()->json([
                'status' => 200,
                'data' => [
                    'user_type' => 'giang_vien',
                    'stats' => [
                        'students' => $studentsCount,
                        'pending' => $pendingCount,
                        'confirmed' => $confirmedCount
                    ],
                    'bookings' => $bookingRequests
                ]
            ]);
        }
    }

    public function updateBookingStatus(Request $r)
    {
        $id = $r->input('id');
        $status = $r->input('trang_thai'); // 'da_xac_nhan' or 'tu_choi'

        if (!in_array($status, ['da_xac_nhan', 'tu_choi'])) {
            return response()->json(['status' => 400, 'message' => 'Invalid status'], 400);
        }

        DB::table('lich_hen')->where('id', $id)->update([
            'trang_thai' => $status,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }

    public function forceSyncStudentApi(Request $r)
    {
        $email = $r->session()->get(\App\VLUTE::SESSION_Email);
        if (!$email) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated'], 401);
        }
        $maDoiTuong = explode('@', $email)[0];
        $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
        if (!$sv) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        self::syncStudentDataFromApi($maDoiTuong, $email, $sv->id);

        return response()->json([
            'status' => 200,
            'message' => 'Đồng bộ dữ liệu từ hệ thống Đào tạo thành công!'
        ]);
    }

    /**
     * Helper to call Training API (daotao.vlute.edu.vn) and automatically map dynamic attributes to DB
     */
    public static function syncStudentDataFromApi($mssv, $email, $studentId)
    {
        try {
            $apiUrl = \App\VLUTE::getCaiDat('DAOTAO_API_URL', 'https://daotao.vlute.edu.vn/api/admin/tt-sinh-vien');
            $apiToken = \App\VLUTE::getCaiDat('DAOTAO_API_TOKEN');

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken
            ])->withOptions([
                'verify' => false
            ])->get($apiUrl, [
                'mssv' => $mssv,
                'gmail' => $email
            ]);

            if ($response->successful()) {
                $svInfo = $response->json();
                if (isset($svInfo['data'])) {
                    $svInfo = $svInfo['data'];
                }

                if (is_array($svInfo)) {
                    $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'sinh_vien')->first();
                    if (!$loaiDoiTuong) {
                        $typeId = DB::table('loai_doi_tuong')->insertGetId([
                            'ma_loai' => 'sinh_vien',
                            'ten_loai' => 'Sinh viên',
                            'mo_ta' => 'Quản lý thông tin sinh viên cơ bản & thuộc tính học tập mở rộng',
                            'ngay_tao' => now(),
                        ]);
                    } else {
                        $typeId = $loaiDoiTuong->id;
                    }

                    $maxOrder = DB::table('danh_muc_truong')->where('loai_doi_tuong_id', $typeId)->max('thu_tu') ?? 0;

                    foreach ($svInfo as $key => $value) {
                        if (in_array($key, ['mssv', 'ho_ten', 'ten_sinh_vien', 'id', 'id_sinh_vien', 'email'])) {
                            continue;
                        }
                        if (is_array($value) || is_object($value)) {
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        if ($value === null) {
                            continue;
                        }

                        $attr = DB::table('danh_muc_truong')
                            ->where('loai_doi_tuong_id', $typeId)
                            ->where('ma_truong', $key)
                            ->first();

                        if (!$attr) {
                            $maxOrder++;
                            $phanNhom = 'Thông tin bổ sung';
                            if (in_array($key, ['ngay_sinh', 'gioi_tinh', 'so_dien_thoai', 'so_dien_thoai_sv', 'cccd', 'cmnd'])) {
                                $phanNhom = 'Thông tin Lý lịch & Cá nhân';
                            } elseif (in_array($key, ['nganh_hoc', 'lop_sinh_hoat', 'chuyen_nganh', 'nien_khoa', 'he_dao_tao'])) {
                                $phanNhom = 'Thông tin Đào tạo & Lớp học';
                            } elseif (in_array($key, ['anh_the_3x4', 'tep_minh_chung', 'hinh_anh', 'file_minh_chung'])) {
                                $phanNhom = 'Hồ sơ đính kèm';
                            }

                            $kieuDuLieu = 'text';
                            if (str_contains($key, 'ngay') || str_contains($key, 'date')) {
                                $kieuDuLieu = 'date';
                            } elseif (str_contains($key, 'anh') || str_contains($key, 'image')) {
                                $kieuDuLieu = 'image';
                            } elseif (str_contains($key, 'file') || str_contains($key, 'tep') || str_contains($key, 'pdf')) {
                                $kieuDuLieu = 'file';
                            }

                            $attrId = DB::table('danh_muc_truong')->insertGetId([
                                'loai_doi_tuong_id' => $typeId,
                                'ma_truong' => $key,
                                'ten_truong' => ucwords(str_replace('_', ' ', $key)),
                                'phan_nhom' => $phanNhom,
                                'kieu_du_lieu' => $kieuDuLieu,
                                'thu_tu' => $maxOrder,
                                'trang_thai' => true,
                                'ngay_tao' => now()
                            ]);
                        } else {
                            $attrId = $attr->id;
                        }

                        DB::table('gia_tri_thong_tin')->updateOrInsert(
                            [
                                'doi_tuong_id' => $studentId,
                                'truong_id' => $attrId
                            ],
                            [
                                'gia_tri' => (string)$value,
                                'ngay_tao' => now()
                            ]
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error syncing student API data for MSSV {$mssv}: " . $e->getMessage());
        }
    }

    public static function getMasterDoiTuongId($loaiDoiTuongId, $maDoiTuong, $tenHienThi = '')
    {
        $dt = DB::table('doi_tuong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('ma_doi_tuong', $maDoiTuong)
            ->first();
        if ($dt) {
            return $dt->id;
        }
        return DB::table('doi_tuong')->insertGetId([
            'loai_doi_tuong_id' => $loaiDoiTuongId,
            'ma_doi_tuong' => $maDoiTuong,
            'ten_hien_thi' => $tenHienThi ?: $maDoiTuong,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now()
        ]);
    }
}
