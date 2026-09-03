<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\VLUTE;

class NhatKyService
{
    public static function ghiLog(
        string $hanhDong,
        string $moTa = '',
        ?string $bangTacDong = null,
        ?int $idBanGhi = null,
        $duLieuCu = null,
        $duLieuMoi = null
    ) {
        $idTaiKhoan = session(VLUTE::SESSION_IDTaiKhoan) ?? null;
        $email = session(VLUTE::SESSION_Email) ?? session(VLUTE::SESSION_HoTen) ?? 'System';

        $strCu = $duLieuCu !== null ? (is_string($duLieuCu) ? $duLieuCu : json_encode($duLieuCu, JSON_UNESCAPED_UNICODE)) : null;
        $strMoi = $duLieuMoi !== null ? (is_string($duLieuMoi) ? $duLieuMoi : json_encode($duLieuMoi, JSON_UNESCAPED_UNICODE)) : null;

        DB::table('nhat_ky_he_thong')->insert([
            'id_tai_khoan' => $idTaiKhoan,
            'ten_dang_nhap' => $email,
            'hanh_dong' => $hanhDong,
            'mo_ta' => $moTa,
            'bang_tac_dong' => $bangTacDong,
            'id_ban_ghi' => $idBanGhi,
            'du_lieu_cu' => $strCu,
            'du_lieu_moi' => $strMoi,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'ngay_tao' => now(),
        ]);
    }
}
