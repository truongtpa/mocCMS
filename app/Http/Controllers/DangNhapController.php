<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class DangNhapController extends Controller
{
    public function dangNhap()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $nguoiDung = Socialite::driver('keycloak')->user();
        } catch (\Throwable $e) {
            return redirect()->route('DangNhapController.dangNhap')->with('error', 'Phiên đăng nhập không hợp lệ. Vui lòng thử lại.');
        }

        $email = trim($nguoiDung->getEmail() ?? '');
        $taiKhoan = DB::table('tai_khoan')->where('email', $email)->first();

        if (! $taiKhoan) {
            return redirect()->route('DangNhapController.dangNhap')->with('error', 'Tài khoản chưa được cấp quyền sử dụng hệ thống.');
        }

        $request->session()->regenerate();
        $request->session()->put('tai_khoan', [
            'id' => $taiKhoan->id_tai_khoan ?? $taiKhoan->id ?? null,
            'ho_ten' => $taiKhoan->ho_ten,
            'email' => $taiKhoan->email,
        ]);

        return redirect()->intended('/');
    }

    public function dangXuat(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('DangNhapController.dangNhap');
    }
}
