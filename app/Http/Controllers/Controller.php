<?php

namespace App\Http\Controllers;

use App\QuyenTruyCap;
use App\Models\NhatKy;
use App\Models\TaiKhoanChucVu;
use App\Response;
use App\VLUTE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

abstract class Controller
{
    protected $boQuaKiemTraQuyen = [];

    protected $boQuaGhiLog = [];

    public function callAction($method, $parameters)
    {
        $id_tai_khoan = session(VLUTE::SESSION_IDTaiKhoan);
        $ten_func = class_basename(static::class).'.'.$method;

        if ($this->canKiemTraQuyen($method) && ! QuyenTruyCap::duocChay($id_tai_khoan, $ten_func)) {
            return Response::Error('Không có quyền', 'Bạn không được cấp quyền để thực hiện chức năng này. Liên hệ phòng TC-HC để hỗ trợ xử lý.');
        }

        $ketQua = $this->{$method}(...array_values($parameters));

        if ($this->canGhiLog($method)) {
            NhatKy::ghi($id_tai_khoan ? (int) $id_tai_khoan : null, $ten_func);
        }

        return $ketQua;
    }

    protected function taiKhoanHienTai(Request $request): object
    {
        $id = (int) $request->session()->get(VLUTE::SESSION_IDTaiKhoan);
        $user = TaiKhoanChucVu::joinChucVuChinh(DB::table('tai_khoan'), 'tai_khoan', 'chuc_vu', 'don_vi')
            ->where('tai_khoan.id_tai_khoan', $id)
            ->select(
                'tai_khoan.*', 'tkcv_chinh.id_don_vi',
                'don_vi.ten_don_vi', 'chuc_vu.ten_chuc_vu', 'chuc_vu.truong_don_vi'
            )->first();
        abort_unless($user, 401);

        return $user;
    }

    protected function kiemTraDuLieu(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return Response::Error('Dữ liệu chưa hợp lệ', $validator->errors()->all());
        }

        return null;
    }

    private function canGhiLog($method)
    {
        if (! env('BAT_GHI_LOG', true)) {
            return false;
        }

        if (in_array($method, $this->boQuaGhiLog, true)) {
            return false;
        }

        return $this->dungHttpMethod();
    }

    private function dungHttpMethod()
    {
        $cauHinh = trim((string) env('GHI_LOG_HTTP_METHOD', 'POST,PUT,PATCH,DELETE'));

        if ($cauHinh === '*') {
            return true;
        }

        $ds = collect(explode(',', $cauHinh))
            ->map(fn ($m) => strtoupper(trim($m)))
            ->filter()
            ->all();

        return in_array(request()->method(), $ds, true);
    }

    private function canKiemTraQuyen($method)
    {
        if (! env('BAT_KIEM_TRA_FUNCS', true)) {
            return false;
        }

        if (in_array($method, $this->boQuaKiemTraQuyen, true)) {
            return false;
        }

        return true;
    }
}
