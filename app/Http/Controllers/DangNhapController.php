<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\VLUTE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;

class DangNhapController extends Controller
{
    protected $boQuaKiemTraQuyen = ['dangNhapKeycloak', 'callbackKeycloak', 'dangXuat', 'thayDoiMatKhau', 'trangChu'];

    public function dangNhapKeycloak() {
        return Socialite::driver('keycloak')->redirect();
    }

    public function callbackKeycloak(Request $request)
    {

        $user = Socialite::driver('keycloak')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->stateless()
            ->user();

        $token = $user->token;
        $tttk = $this->decodeJWTPayloadOnly($token);
        if (!isset($tttk->email)) {
            Log::error('No email in Keycloak token', ['token_payload' => $tttk]);
            return "Không tìm thấy email trong token.";
        }

        $email = $tttk->email;

        $taiKhoan = DB::table('tai_khoan')
            ->where('email', $email)
            ->first();

        if (!$taiKhoan) {
            Log::warning('Email not found in tai_khoan table', ['email' => $email]);
            return "Email không tồn tại trong hệ thống.";
        }

        // Lưu thông tin vào session
        $request->session()->put(VLUTE::SESSION_IDTaiKhoan, $taiKhoan->id_tai_khoan);
        $request->session()->put(VLUTE::SESSION_HoTen, $taiKhoan->ho_ten);
        $request->session()->put(VLUTE::SESSION_Email, $taiKhoan->email);

        if (session()->has('redirect_after_login')) {
            return redirect(session('redirect_after_login'));
        } else {
            return redirect('/home');
        }
    }



    public function decodeJWTPayloadOnly($token)
    {
        $tks = explode('.', $token);
        if (count($tks) != 3) {
            return null;
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        $input = $bodyb64;
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        $input = (base64_decode(strtr($input, '-_', '+/')));

        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = strlen((string)PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{' . $max_int_length . ',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }
        return $obj;
    }

    public function dangXuat()
    {

        session()->invalidate();
        Cookie::queue(Cookie::forget('auth_tttk'));
        return redirect(Socialite::driver('keycloak')
            ->getLogoutUrl(env('APP_URL')));
    }

    public function thayDoiMatKhau()
    {
        session()->invalidate();
        Cookie::queue(Cookie::forget('auth_tttk'));
        $KEYCLOAK_BASE_URL = env('KEYCLOAK_BASE_URL');
        $REALM = env('KEYCLOAK_REALM');
        return redirect($KEYCLOAK_BASE_URL . "realms/" . $REALM . "/account/#/security/signingin");
    }

    public function trangChu(Request $request)
    {
        $id_tkhoan = $request->session()->get(VLUTE::SESSION_IDTaiKhoan);
        return view('app', [
            'data' => (new TaiKhoan())->thongTinCaNhan(['id_tai_khoan' => $id_tkhoan]),
            'show_views' => \App\QuyenTruyCap::layShowViews($id_tkhoan)
        ]);
    }
}
