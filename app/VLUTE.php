<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VLUTE
{
    public const SESSION_HoTen = 'SESSION_HoTen';
    public const SESSION_Email = 'SESSION_Email';
    public const SESSION_IDTaiKhoan = 'SESSION_IDTaiKhoan';
    public const SESSION_IDDonVi = 'SESSION_IDDonVi';

    public static function checkPermission($permissionKey, $idTaiKhoan = null)
    {
        if (!$idTaiKhoan) {
            $idTaiKhoan = session()->get(self::SESSION_IDTaiKhoan);
        }

        $email = session()->get(self::SESSION_Email);

        if (!$idTaiKhoan && $email) {
            $tk = DB::table('tai_khoan')->where('email', $email)->first();
            if ($tk) {
                $idTaiKhoan = $tk->id_tai_khoan;
                session()->put(self::SESSION_IDTaiKhoan, $idTaiKhoan);
            }
        }

        if (!$idTaiKhoan) {
            return false;
        }

        // 1. Lấy danh sách id_quyen_nhom được gán + id_quyen_nhom mặc định (mac_dinh = 1)
        $assignedGroupIds = DB::table('quyen_nhom_tai_khoan')
            ->where('id_tai_khoan', (string)$idTaiKhoan)
            ->pluck('id_quyen_nhom')
            ->toArray();

        $defaultGroupIds = DB::table('quyen_nhom')
            ->where('mac_dinh', 1)
            ->pluck('id_quyen_nhom')
            ->toArray();

        $groupIds = array_values(array_unique(array_merge($assignedGroupIds, $defaultGroupIds)));

        // 2. Admin hệ thống (nhóm id_quyen_nhom = 1) có toàn quyền
        if (in_array(1, $groupIds)) {
            return true;
        }

        // 3. Lấy danh sách funcs từ các quyền chi tiết thuộc nhóm
        $allowedFuncs = [];
        if (!empty($groupIds)) {
            $funcsList = DB::table('quyen_nhom_chi_tiet')
                ->join('quyen_chi_tiet', 'quyen_nhom_chi_tiet.id_quyen_chi_tiet', '=', 'quyen_chi_tiet.id_quyen_chi_tiet')
                ->whereIn('quyen_nhom_chi_tiet.id_quyen_nhom', $groupIds)
                ->pluck('quyen_chi_tiet.funcs')
                ->toArray();

            foreach ($funcsList as $funcsStr) {
                if (!empty($funcsStr)) {
                    foreach (array_map('trim', explode(',', $funcsStr)) as $item) {
                        if (!empty($item)) {
                            $allowedFuncs[] = $item;
                        }
                    }
                }
            }
        }

        // 4. Bổ sung quyền mặc định từ bảng cai_dat (nếu có)
        if ($email && Schema::hasTable('cai_dat')) {
            $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
            $settingKey = $isStudent ? 'DEFAULT_PERMISSION_SINH_VIEN' : 'DEFAULT_PERMISSION_GIANG_VIEN';
            $defaultSetting = DB::table('cai_dat')->where('khoa', $settingKey)->first();

            if ($defaultSetting && !empty($defaultSetting->gia_tri)) {
                $val = trim($defaultSetting->gia_tri);
                $decoded = json_decode($val, true);
                $settingPerms = is_array($decoded) ? $decoded : array_map('trim', explode(',', $val));
                foreach ($settingPerms as $sp) {
                    if (!empty($sp)) {
                        $allowedFuncs[] = trim($sp);
                    }
                }
            }
        }

        return in_array($permissionKey, array_unique($allowedFuncs));
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
                $idTaiKhoan = $tk->id_tai_khoan;
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

        $assignedGroupIds = DB::table('quyen_nhom_tai_khoan')
            ->where('id_tai_khoan', (string)$idTaiKhoan)
            ->pluck('id_quyen_nhom')
            ->toArray();

        $defaultGroupIds = DB::table('quyen_nhom')
            ->where('mac_dinh', 1)
            ->pluck('id_quyen_nhom')
            ->toArray();

        $groupIds = array_values(array_unique(array_merge($assignedGroupIds, $defaultGroupIds)));
        $isAdmin = in_array(1, $groupIds);

        if ($isAdmin) {
            $allDetails = DB::table('quyen_chi_tiet')->get();
        } else {
            $allDetails = DB::table('quyen_nhom_chi_tiet')
                ->join('quyen_chi_tiet', 'quyen_nhom_chi_tiet.id_quyen_chi_tiet', '=', 'quyen_chi_tiet.id_quyen_chi_tiet')
                ->whereIn('quyen_nhom_chi_tiet.id_quyen_nhom', $groupIds)
                ->select('quyen_chi_tiet.funcs', 'quyen_chi_tiet.show_views')
                ->get();
        }

        $allowedFuncs = [];
        $allowedViews = [];

        foreach ($allDetails as $detail) {
            if (!empty($detail->funcs)) {
                foreach (array_map('trim', explode(',', $detail->funcs)) as $f) {
                    if (!empty($f)) $allowedFuncs[] = $f;
                }
            }
            if (!empty($detail->show_views)) {
                foreach (array_map('trim', explode(',', $detail->show_views)) as $v) {
                    if (!empty($v)) $allowedViews[] = $v;
                }
            }
        }

        // Bổ sung quyền từ cai_dat nếu có
        if ($email && Schema::hasTable('cai_dat')) {
            $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
            $settingKey = $isStudent ? 'DEFAULT_PERMISSION_SINH_VIEN' : 'DEFAULT_PERMISSION_GIANG_VIEN';
            $defaultSetting = DB::table('cai_dat')->where('khoa', $settingKey)->first();

            if ($defaultSetting && !empty($defaultSetting->gia_tri)) {
                $val = trim($defaultSetting->gia_tri);
                $decoded = json_decode($val, true);
                $settingPerms = is_array($decoded) ? $decoded : array_map('trim', explode(',', $val));
                foreach ($settingPerms as $sp) {
                    $item = trim($sp);
                    if (!empty($item)) {
                        $allowedFuncs[] = $item;
                        $allowedViews[] = $item;
                    }
                }
            }
        }

        return [
            'funcs' => array_values(array_unique($allowedFuncs)),
            'show_views' => array_values(array_unique($allowedViews)),
            'is_admin' => $isAdmin
        ];
    }
}
