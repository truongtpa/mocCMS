<?php

namespace App\Http\Controllers;

use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    private function checkUserPermission($permissionKey)
    {
        return \App\VLUTE::checkPermission($permissionKey);
    }

    public function getDsApiKey()
    {
        if (!$this->checkUserPermission('ApiKeyController.getDsApiKey')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $list = DB::table('api_key')->orderBy('id', 'desc')->get();
        return Response::Success($list, 'Lấy danh sách API Key thành công');
    }

    public function putApiKey(Request $request)
    {
        if (!$this->checkUserPermission('ApiKeyController.putApiKey')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $tenUngDung = trim($request->input('ten_ung_dung', ''));
        $moTa = trim($request->input('mo_ta', ''));
        $trangThai = intval($request->input('trang_thai', 1));

        $errors = [];
        if (empty($tenUngDung)) {
            $errors[] = 'Tên ứng dụng không được bỏ trống';
        }
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if ($id) {
            $apiKeyItem = DB::table('api_key')->where('id', $id)->first();
            if (!$apiKeyItem) {
                return Response::Error('Lỗi', 'Không tìm thấy API Key');
            }

            DB::table('api_key')->where('id', $id)->update([
                'ten_ung_dung' => $tenUngDung,
                'mo_ta' => $moTa,
                'trang_thai' => $trangThai,
                'ngay_cap_nhat' => now()
            ]);

            return Response::Success([], 'Cập nhật API Key thành công');
        }

        $key = 'vlute_sk_' . Str::random(32);
        $newId = DB::table('api_key')->insertGetId([
            'ten_ung_dung' => $tenUngDung,
            'api_key' => $key,
            'mo_ta' => $moTa,
            'trang_thai' => $trangThai,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now()
        ]);

        return Response::Success(['id' => $newId, 'api_key' => $key], 'Tạo API Key thành công');
    }

    public function deleteApiKey(Request $request, $id)
    {
        if (!$this->checkUserPermission('ApiKeyController.deleteApiKey')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $count = DB::table('api_key')->where('id', $id)->delete();
        if ($count === 0) {
            return Response::Error('Lỗi', 'Không tìm thấy API Key để xóa');
        }

        return Response::Success([], 'Xóa API Key thành công');
    }

    public function getThongTinSinhVien(Request $request)
    {
        $apiKey = $request->header('X-API-KEY') ?: $request->input('api_key');
        if (empty($apiKey)) {
            return Response::Error('Xác thực thất bại', 'Thiếu API Key trong yêu cầu (qua header X-API-KEY hoặc tham số api_key)');
        }

        $keyRecord = DB::table('api_key')
            ->where('api_key', $apiKey)
            ->where('trang_thai', 1)
            ->first();

        if (!$keyRecord) {
            return Response::Error('Xác thực thất bại', 'API Key không hợp lệ hoặc đã bị khóa');
        }

        $mssv = trim($request->input('mssv', ''));
        if (empty($mssv)) {
            return Response::Error('Thiếu dữ liệu', 'Tham số mssv không được bỏ trống');
        }

        $sv = DB::table('sinh_vien')->where('mssv', $mssv)->first();
        if (!$sv) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy sinh viên với MSSV: ' . $mssv);
        }

        $loaiDoiTuong = DB::table('loai_doi_tuong')->where('ma_loai', 'sinh_vien')->first();
        $thuocTinh = [];

        if ($loaiDoiTuong) {
            $masterDoiTuongId = StudentPortalController::getMasterDoiTuongId($loaiDoiTuong->id, $sv->mssv, $sv->ho_ten);
            $possibleIds = array_unique([$masterDoiTuongId, $sv->id]);

            $fields = DB::table('danh_muc_truong')
                ->where('loai_doi_tuong_id', $loaiDoiTuong->id)
                ->where('trang_thai', true)
                ->orderBy('thu_tu', 'asc')
                ->get();

            foreach ($fields as $field) {
                $val = DB::table('gia_tri_thong_tin')
                    ->whereIn('doi_tuong_id', $possibleIds)
                    ->where('truong_id', $field->id)
                    ->orderBy('id', 'desc')
                    ->first();
                $rawVal = $val ? $val->gia_tri : null;
                if (in_array($field->kieu_du_lieu, ['image', 'file']) && !empty($rawVal)) {
                    $rawVal = \App\Services\S3Services::urlCongKhai($rawVal);
                }
                $thuocTinh[$field->ma_truong] = [
                    'ten_truong' => $field->ten_truong,
                    'kieu_du_lieu' => $field->kieu_du_lieu,
                    'gia_tri' => $rawVal
                ];
            }
        }

        $achievements = DB::table('thanh_tich')
            ->where('sinh_vien_id', $sv->id)
            ->orderBy('id', 'desc')
            ->get();

        $data = [
            'thong_tin_co_ban' => [
                'id' => $sv->id,
                'mssv' => $sv->mssv,
                'ho_ten' => $sv->ho_ten,
                'email' => $sv->email,
                'created_at' => $sv->created_at,
                'updated_at' => $sv->updated_at
            ],
            'thuoc_tinh_mo_rong' => $thuocTinh,
            'giai_thuong_thanh_tich' => $achievements
        ];

        return Response::Success($data, 'Lấy thông tin sinh viên thành công');
    }
}
