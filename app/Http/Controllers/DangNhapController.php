<?php

namespace App\Http\Controllers;

use App\VLUTE;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class DangNhapController extends Controller
{
    public function dangNhapKeycloak() {
        return Socialite::driver('keycloak')->redirect();
    }
    public function callbackKeycloak(Request $request)
    {

        $user = Socialite::driver('keycloak')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false,]))
            ->stateless()
            ->user();

        $token = $user->token;
        $tttk = $this->decodeJWTPayloadOnly($token);

        if (isset($tttk->email)) {
            // Lấy email từ payload
            $email = $tttk->email;
            $taiKhoan = null;

            // Extract MSSV/MSGV từ email (phần trước @)
            $maDoiTuong = explode('@', $email)[0];
            $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
            
            try {
                if (!$isStudent) {
                    // ----------------------------------------------------
                    // ----------------------------------------------------
                    // LUỒNG GIẢNG VIÊN (Lưu thông tin vào bảng giang_vien & tai_khoan)
                    // ----------------------------------------------------
                    $name = $tttk->name ?? 'Giảng viên (' . $maDoiTuong . ')';
                    DB::table('giang_vien')->updateOrInsert(
                        ['email' => $email],
                        [
                            'ho_ten' => $name,
                            'id_don_vi' => 1,
                            'updated_at' => now()
                        ]
                    );
                    $gv = DB::table('giang_vien')->where('email', $email)->first();

                    DB::table('tai_khoan')->updateOrInsert(
                        ['email' => $email],
                        [
                            'ho_ten' => $name,
                            'ngay_tao' => now()
                        ]
                    );
                    $tk = DB::table('tai_khoan')->where('email', $email)->first();
                    if ($tk) {
                        $hasRole = DB::table('quyen_nhom_tai_khoan')
                            ->where('id_tai_khoan', (string)$tk->id)
                            ->exists();
                        if (!$hasRole) {
                            $setting = DB::table('cai_dat')->where('khoa', 'DEFAULT_PERMISSION_GIANG_VIEN')->first();
                            if ($setting && !empty($setting->gia_tri)) {
                                $val = trim($setting->gia_tri);
                                $defRole = DB::table('quyen_nhom')->where('ma_vai_tro', $val)->first();
                                if ($defRole) {
                                    DB::table('quyen_nhom_tai_khoan')->insert([
                                        'id_tai_khoan' => (string)$tk->id,
                                        'id_quyen_nhom' => $defRole->id_quyen_nhom,
                                        'ngay_tao' => now(),
                                        'ngay_cap_nhat' => now()
                                    ]);
                                }
                            }
                        }
                    }

                    $taiKhoan = (object)[
                        'id_tai_khoan' => $tk ? $tk->id : $gv->id,
                        'ho_ten' => $name,
                        'email' => $email,
                        'id_don_vi' => $gv->id_don_vi
                    ];
                } else {
                    // ----------------------------------------------------
                    // LUỒNG SINH VIÊN (Gọi API đào tạo & Lưu thông tin sinh viên + EAV)
                    // ----------------------------------------------------
                    $apiUrl = \App\VLUTE::getCaiDat('DAOTAO_API_URL', 'https://daotao.vlute.edu.vn/api/admin/tt-sinh-vien');
                    $apiToken = \App\VLUTE::getCaiDat('DAOTAO_API_TOKEN');

                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiToken
                    ])->withOptions([
                        'verify' => false
                    ])->get($apiUrl, [
                        'mssv' => $maDoiTuong,
                        'gmail' => $email
                    ]);

                    if (!$response->successful()) {
                        abort(500, 'Không thể tải thông tin sinh viên từ hệ thống đào tạo.');
                    }

                    $svInfo = $response->json();
                    if (isset($svInfo['data'])) {
                        $svInfo = $svInfo['data'];
                    }

                    // 1. Lưu thông tin cơ bản vào bảng sinh_vien & tai_khoan
                    $hoTen = $svInfo['ho_ten'] ?? $svInfo['ten_sinh_vien'] ?? null;
                    if (!$hoTen && isset($svInfo['ho'], $svInfo['ten'])) {
                        $hoTen = trim($svInfo['ho'] . ' ' . $svInfo['ten']);
                    }
                    if (!$hoTen) {
                        $hoTen = 'Sinh viên ' . $maDoiTuong;
                    }
                    DB::table('sinh_vien')->updateOrInsert(
                        ['mssv' => $maDoiTuong],
                        [
                            'ho_ten' => $hoTen,
                            'email' => $email,
                            'updated_at' => now()
                        ]
                    );
                    $sv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();

                    DB::table('tai_khoan')->updateOrInsert(
                        ['email' => $email],
                        [
                            'ho_ten' => $hoTen,
                            'ngay_tao' => now()
                        ]
                    );
                    $tk = DB::table('tai_khoan')->where('email', $email)->first();
                    if ($tk) {
                        $hasRole = DB::table('quyen_nhom_tai_khoan')
                            ->where('id_tai_khoan', (string)$tk->id)
                            ->exists();
                        if (!$hasRole) {
                            $setting = DB::table('cai_dat')->where('khoa', 'DEFAULT_PERMISSION_SINH_VIEN')->first();
                            if ($setting && !empty($setting->gia_tri)) {
                                $val = trim($setting->gia_tri);
                                $defRole = DB::table('quyen_nhom')->where('ma_vai_tro', $val)->first();
                                if ($defRole) {
                                    DB::table('quyen_nhom_tai_khoan')->insert([
                                        'id_tai_khoan' => (string)$tk->id,
                                        'id_quyen_nhom' => $defRole->id_quyen_nhom,
                                        'ngay_tao' => now(),
                                        'ngay_cap_nhat' => now()
                                    ]);
                                }
                            }
                        }
                    }

                    // 2. Lưu các trường động vào EAV mở rộng sinh viên
                    \App\Http\Controllers\StudentPortalController::syncStudentDataFromApi($maDoiTuong, $email, $sv->id);

                    $taiKhoan = (object)[
                        'id_tai_khoan' => $tk ? $tk->id : $sv->id,
                        'ho_ten' => $hoTen,
                        'email' => $email
                    ];
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error inside Keycloak SSO callback logic: ' . $e->getMessage());
                throw $e;
            }

            if ($taiKhoan) {
                // Gán session
                $request->session()->put(VLUTE::SESSION_IDTaiKhoan, $taiKhoan->id_tai_khoan);
                $request->session()->put(VLUTE::SESSION_HoTen, $taiKhoan->ho_ten);
                $request->session()->put(VLUTE::SESSION_Email, $taiKhoan->email);
                if (isset($taiKhoan->id_don_vi)) {
                    $request->session()->put(VLUTE::SESSION_IDDonVi, $taiKhoan->id_don_vi);
                } else {
                    $request->session()->forget(VLUTE::SESSION_IDDonVi);
                }
                $b = (string) \Illuminate\Support\Str::uuid();
                $request->session()->put('b', $b);
                $cookie = cookie()->forever('b', $b, null, null, false, false);
                return redirect()->to('/admin')->withCookie($cookie);
            }
            return redirect()->action([DangNhapController::class, 'redirectKhongCoQuyen']);
        }
        session()->invalidate();
        return redirect()->action([DangNhapController::class, 'redirectKhongCoQuyen']);
    }

    function decodeJWTPayloadOnly($token)
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

    public function dangXuat(){
        session()->invalidate();
        return redirect(Socialite::driver('keycloak')
            ->getLogoutUrl(env('APP_URL')));
    }

    public function thayDoiMatKhau(){
        session()->invalidate();
        $KEYCLOAK_BASE_URL = env('KEYCLOAK_BASE_URL');
        $REALM = env('KEYCLOAK_REALM');
        return redirect($KEYCLOAK_BASE_URL . "realms/" . $REALM . "/account/#/security/signingin");
    }

    public function redirectKhongCoQuyen(Request $request)
    {
        return view('emptyPermission');
    }

    public function generateToken(Request $request)
    {
        $data = $request->input('data');
        $time = Carbon::now()->addMinutes(10);
        $dataEncrypt = '';

        if(strlen($data) > 5){
            $raw = json_encode([
                'data' => $data,
                'time' => $time
            ]);
            $dataEncrypt = Crypt::encrypt($raw);
        }

        return view('generate-token', [
            'data' => $data,
            'dataEncrypt' => $dataEncrypt
        ]);
    }

    public function trangChu(Request $request)
    {
        $userId = $request->session()->get(VLUTE::SESSION_IDTaiKhoan);
        $email = $request->session()->get(VLUTE::SESSION_Email);
        $hoTen = $request->session()->get(VLUTE::SESSION_HoTen);

        return view('app', [
            'data' => [
                'id_tai_khoan' => $userId,
                'email' => $email,
                'ho_ten' => $hoTen
            ],
            'show_views' => []
        ]);
    }
}
