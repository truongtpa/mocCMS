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
                    // LUỒNG GIẢNG VIÊN (Lưu thông tin vào bảng giang_vien)
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

                    // Tự động gán vai trò 'giang_vien'
                    $role = DB::table('vai_tro')->where('ma_vai_tro', 'giang_vien')->first();
                    if (!$role) {
                        DB::table('vai_tro')->insert([
                            'ma_vai_tro' => 'giang_vien',
                            'ten_vai_tro' => 'Giảng viên',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $role = DB::table('vai_tro')->where('ma_vai_tro', 'giang_vien')->first();
                    }

                    DB::table('vai_tro_nguoi_dung')->updateOrInsert(
                        [
                            'user_id' => $gv->id_giang_vien,
                            'user_type' => 'giang_vien',
                            'vai_tro_id' => $role->id
                        ],
                        ['updated_at' => now()]
                    );

                    $taiKhoan = (object)[
                        'id_tai_khoan' => $gv->id_giang_vien,
                        'ho_ten' => $gv->ho_ten,
                        'email' => $gv->email,
                        'id_don_vi' => $gv->id_don_vi
                    ];
                } else {
                    // ----------------------------------------------------
                    // LUỒNG SINH VIÊN (Gọi API đào tạo & Lưu thông tin sinh viên + EAV)
                    // ----------------------------------------------------
                    $apiUrl = env('DAOTAO_API_URL', 'https://daotao.vlute.edu.vn/api/admin/tt-sinh-vien');
                    $apiToken = env('DAOTAO_API_TOKEN', 'tgHkYe3wgiSJcZ5hw3Ze1v4nTuQFTG7b');
                    $apiCookie = env('DAOTAO_API_COOKIE', 'laravel_session=xT67QhnCJZTg1L1tCE16gDbAUketgtN0AhHDS7ug');

                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiToken,
                        'Cookie' => $apiCookie
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

                    // 1. Lưu thông tin cơ bản vào bảng sinh_vien
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

                    // Tự động gán vai trò 'sinh_vien'
                    $role = DB::table('vai_tro')->where('ma_vai_tro', 'sinh_vien')->first();
                    if (!$role) {
                        DB::table('vai_tro')->insert([
                            'ma_vai_tro' => 'sinh_vien',
                            'ten_vai_tro' => 'Sinh viên',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $role = DB::table('vai_tro')->where('ma_vai_tro', 'sinh_vien')->first();
                    }

                    DB::table('vai_tro_nguoi_dung')->updateOrInsert(
                        [
                            'user_id' => $sv->id,
                            'user_type' => 'sinh_vien',
                            'vai_tro_id' => $role->id
                        ],
                        ['updated_at' => now()]
                    );

                    // 2. Lưu các trường động vào EAV mở rộng sinh viên
                    \App\Http\Controllers\StudentPortalController::syncStudentDataFromApi($maDoiTuong, $email, $sv->id);

                    $taiKhoan = (object)[
                        'id_tai_khoan' => $sv->id,
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
