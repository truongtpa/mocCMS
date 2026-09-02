<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VLUTE
{
    const SESSION_IDTaiKhoan = 'id_tai_khoan';
    const SESSION_Email = 'email';
    const SESSION_HoTen = 'ho_ten';
    const SESSION_IDDonVi = 'id_don_vi';

    public static function checkPermission($permissionKey, $idTaiKhoan = null)
    {
        if (!$idTaiKhoan) {
            $idTaiKhoan = session()->get(self::SESSION_IDTaiKhoan);
        }

        $email = session()->get(self::SESSION_Email);

        if (!$idTaiKhoan && $email) {
            $tk = DB::table('tai_khoan')->where('email', $email)->first();
            if ($tk) {
                $idTaiKhoan = $tk->id;
                session()->put(self::SESSION_IDTaiKhoan, $idTaiKhoan);
            }
        }

        if (!$idTaiKhoan) {
            return false;
        }

        // 1. Super Admin CHỈ xác định theo email cấu hình trong file .env (SUPER_ADMIN_EMAILS)
        $superAdminEmails = array_filter(array_map('trim', explode(',', env('SUPER_ADMIN_EMAILS', ''))));
        $isSuperAdmin = !empty($email) && in_array($email, $superAdminEmails);

        if ($isSuperAdmin) {
            return true;
        }

        // 2. Lấy các nhóm quyền được gán trực tiếp trong quyen_nhom_tai_khoan
        $assignedGroupIds = DB::table('quyen_nhom_tai_khoan')
            ->where('id_tai_khoan', (string)$idTaiKhoan)
            ->pluck('id_quyen_nhom')
            ->toArray();

        $allowedPerms = [];

        if (!empty($assignedGroupIds)) {
            $allowedPerms = DB::table('quyen_nhom_chi_tiet')
                ->join('quyen_chi_tiet', 'quyen_nhom_chi_tiet.id_quyen_chi_tiet', '=', 'quyen_chi_tiet.id_quyen_chi_tiet')
                ->whereIn('quyen_nhom_chi_tiet.id_quyen_nhom', $assignedGroupIds)
                ->pluck('quyen_chi_tiet.funcs')
                ->toArray();
        } else {
            // Đọc cấu hình vai trò mặc định từ bảng cai_dat
            if ($email && \Illuminate\Support\Facades\Schema::hasTable('cai_dat')) {
                $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
                $settingKey = $isStudent ? 'DEFAULT_PERMISSION_SINH_VIEN' : 'DEFAULT_PERMISSION_GIANG_VIEN';
                $defaultSetting = DB::table('cai_dat')->where('khoa', $settingKey)->first();

                if ($defaultSetting && !empty($defaultSetting->gia_tri)) {
                    $val = trim($defaultSetting->gia_tri);
                    $defGroup = DB::table('quyen_nhom')
                        ->where('id_quyen_nhom', $val)
                        ->orWhere('ma_vai_tro', $val)
                        ->orWhere('tieu_de', 'like', "%{$val}%")
                        ->first();

                    if ($defGroup) {
                        $allowedPerms = DB::table('quyen_nhom_chi_tiet')
                            ->join('quyen_chi_tiet', 'quyen_nhom_chi_tiet.id_quyen_chi_tiet', '=', 'quyen_chi_tiet.id_quyen_chi_tiet')
                            ->where('quyen_nhom_chi_tiet.id_quyen_nhom', $defGroup->id_quyen_nhom)
                            ->pluck('quyen_chi_tiet.funcs')
                            ->toArray();
                    }
                }
            }
        }

        // Tách chuỗi funcs phân cách bằng dấu phẩy
        $allFuncs = [];
        foreach ($allowedPerms as $pStr) {
            if (empty($pStr)) continue;
            $items = explode(',', $pStr);
            foreach ($items as $item) {
                $trimmed = trim($item);
                if ($trimmed !== '') {
                    $allFuncs[] = $trimmed;
                }
            }
        }

        return in_array($permissionKey, array_unique($allFuncs));
    }

    public static function getUserPermissions($idTaiKhoan = null)
    {
        if (!$idTaiKhoan) {
            $idTaiKhoan = session()->get(self::SESSION_IDTaiKhoan);
        }

        $email = session()->get(self::SESSION_Email);

        if (!$idTaiKhoan && $email) {
            $tk = DB::table('tai_khoan')->where('email', $email)->first();
            if ($tk) {
                $idTaiKhoan = $tk->id;
                session()->put(self::SESSION_IDTaiKhoan, $idTaiKhoan);
            }
        }

        if (!$idTaiKhoan) {
            return [
                'funcs' => [],
                'show_views' => [],
                'is_admin' => false
            ];
        }

        // 1. Super Admin CHỈ xác định theo email cấu hình trong file .env (SUPER_ADMIN_EMAILS)
        $superAdminEmails = array_filter(array_map('trim', explode(',', env('SUPER_ADMIN_EMAILS', ''))));
        $isAdmin = !empty($email) && in_array($email, $superAdminEmails);

        $assignedGroupIds = DB::table('quyen_nhom_tai_khoan')
            ->where('id_tai_khoan', (string)$idTaiKhoan)
            ->pluck('id_quyen_nhom')
            ->toArray();

        $allFuncs = [];
        $allViews = [];

        if ($isAdmin) {
            $qList = DB::table('quyen_chi_tiet')->get();
            foreach ($qList as $q) {
                if ($q->funcs) {
                    foreach (explode(',', $q->funcs) as $f) {
                        if (trim($f) !== '') $allFuncs[] = trim($f);
                    }
                }
                if ($q->show_views) {
                    foreach (explode(',', $q->show_views) as $v) {
                        if (trim($v) !== '') $allViews[] = trim($v);
                    }
                }
            }
        } else {
            if (empty($assignedGroupIds) && $email && \Illuminate\Support\Facades\Schema::hasTable('cai_dat')) {
                $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
                $settingKey = $isStudent ? 'DEFAULT_PERMISSION_SINH_VIEN' : 'DEFAULT_PERMISSION_GIANG_VIEN';
                $defaultSetting = DB::table('cai_dat')->where('khoa', $settingKey)->first();

                if ($defaultSetting && !empty($defaultSetting->gia_tri)) {
                    $val = trim($defaultSetting->gia_tri);
                    $defGroup = DB::table('quyen_nhom')
                        ->where('id_quyen_nhom', $val)
                        ->orWhere('ma_vai_tro', $val)
                        ->orWhere('tieu_de', 'like', "%{$val}%")
                        ->first();

                    if ($defGroup) {
                        $assignedGroupIds = [$defGroup->id_quyen_nhom];
                    }
                }
            }

            if (!empty($assignedGroupIds)) {
                $qList = DB::table('quyen_nhom_chi_tiet')
                    ->join('quyen_chi_tiet', 'quyen_nhom_chi_tiet.id_quyen_chi_tiet', '=', 'quyen_chi_tiet.id_quyen_chi_tiet')
                    ->whereIn('quyen_nhom_chi_tiet.id_quyen_nhom', $assignedGroupIds)
                    ->select('quyen_chi_tiet.funcs', 'quyen_chi_tiet.show_views')
                    ->get();

                foreach ($qList as $q) {
                    if ($q->funcs) {
                        foreach (explode(',', $q->funcs) as $f) {
                            if (trim($f) !== '') $allFuncs[] = trim($f);
                        }
                    }
                    if ($q->show_views) {
                        foreach (explode(',', $q->show_views) as $v) {
                            if (trim($v) !== '') $allViews[] = trim($v);
                        }
                    }
                }
            }
        }

        return [
            'funcs' => array_values(array_unique($allFuncs)),
            'show_views' => array_values(array_unique($allViews)),
            'is_admin' => $isAdmin
        ];
    }
}
