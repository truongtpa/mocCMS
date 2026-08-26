<?php

namespace App\Http\Controllers;

use App\Models\Quyen;
use App\Models\QuyenChiTiet;
use App\Models\QuyenNhomChiTiet;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuyenController extends Controller
{
    /**
     * Danh sách nhóm quyền (có phân trang + tìm kiếm theo tên nhóm).
     */
    public function getQuyen(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE', 10));
        $keyword = $request->input('s', '');

        $ds = Quyen::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('ten_nhom', 'like', "%{$keyword}%");
            })
            ->withCount('chiTiet')
            ->orderBy('ten_nhom')
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    /**
     * Thêm mới nhóm quyền.
     */
    public function putQuyen(Request $request)
    {
        $ten_nhom = trim($request->ten_nhom ?? '');

        $errors = [];
        if (empty($ten_nhom)) {
            $errors[] = 'Tên nhóm quyền không được bỏ trống';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        // Kiểm tra trùng tên nhóm
        $check = Quyen::where('ten_nhom', $ten_nhom)->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Tên nhóm quyền đã tồn tại');
        }

        try {
            $quyen = Quyen::create([
                'ten_nhom' => $ten_nhom,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now()
            ]);

            return Response::Success(['id_quyen' => $quyen->id_quyen], 'Thêm nhóm quyền thành công');
        } catch (\Exception $e) {
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin nhóm quyền.
     */
    public function updateQuyen(Request $request)
    {
        $id_quyen = $request->id_quyen;
        $ten_nhom = trim($request->ten_nhom ?? '');

        $errors = [];
        if (empty($id_quyen)) {
            $errors[] = 'Thiếu mã nhóm quyền';
        }
        if (empty($ten_nhom)) {
            $errors[] = 'Tên nhóm quyền không được bỏ trống';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        // Kiểm tra trùng tên nhóm với bản ghi khác
        $check = Quyen::where('ten_nhom', $ten_nhom)
            ->where('id_quyen', '!=', $id_quyen)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Tên nhóm quyền đã tồn tại');
        }

        $count = Quyen::where('id_quyen', $id_quyen)
            ->update([
                'ten_nhom' => $ten_nhom,
                'ngay_cap_nhat' => now()
            ]);

        if ($count) {
            return Response::Success('Thành công', 'Cập nhật nhóm quyền thành công');
        }
        return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
    }

    /**
     * Xóa nhóm quyền và toàn bộ quyền chi tiết thuộc nhóm.
     */
    public function deleteQuyen(Request $request)
    {
        $id_quyen = $request->id_quyen;
        if (!$id_quyen) {
            return Response::Error('Lỗi', 'Thiếu mã nhóm quyền');
        }

        DB::beginTransaction();
        try {
            $idsChiTiet = QuyenChiTiet::where('id_quyen', $id_quyen)->pluck('id_quyen_chi_tiet');
            QuyenNhomChiTiet::whereIn('id_quyen_chi_tiet', $idsChiTiet)->delete();
            QuyenChiTiet::where('id_quyen', $id_quyen)->delete();
            $check = Quyen::where('id_quyen', $id_quyen)->delete();

            if ($check) {
                DB::commit();
                return Response::Success('Thành công', 'Xóa nhóm quyền thành công');
            }
            DB::rollBack();
            return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function getQuyenCT(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE', 10));
        $keyword = $request->input('s', '');
        $id_quyen = $request->input('id_quyen');

        if (!$id_quyen) {
            return Response::Error('Lỗi', 'Thiếu mã nhóm quyền');
        }

        $ds = QuyenChiTiet::query()
            ->where('id_quyen', $id_quyen)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('tieu_de', 'like', "%{$keyword}%");
            })
            ->orderBy('tieu_de')
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    public function putQuyenCT(Request $request)
    {
        $id_quyen = $request->id_quyen;
        $tieu_de = trim($request->tieu_de ?? '');
        $funcs = trim($request->funcs ?? '');
        $show_views = trim($request->show_views ?? '');

        $errors = [];
        if (empty($id_quyen)) {
            $errors[] = 'Thiếu mã nhóm quyền';
        }
        if (empty($tieu_de)) {
            $errors[] = 'Tiêu đề không được bỏ trống';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if (!Quyen::where('id_quyen', $id_quyen)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
        }

        $check = QuyenChiTiet::where('id_quyen', $id_quyen)
            ->where('tieu_de', $tieu_de)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Tiêu đề đã tồn tại trong nhóm quyền');
        }

        try {
            $ct = QuyenChiTiet::create([
                'id_quyen' => $id_quyen,
                'tieu_de' => $tieu_de,
                'funcs' => $funcs,
                'show_views' => $show_views,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now()
            ]);

            return Response::Success(['id_quyen_chi_tiet' => $ct->id_quyen_chi_tiet], 'Thêm quyền chi tiết thành công');
        } catch (\Exception $e) {
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function updateQuyenCT(Request $request)
    {
        $id_quyen_chi_tiet = $request->id_quyen_chi_tiet;
        $tieu_de = trim($request->tieu_de ?? '');
        $funcs = trim($request->funcs ?? '');
        $show_views = trim($request->show_views ?? '');

        $errors = [];
        if (empty($id_quyen_chi_tiet)) {
            $errors[] = 'Thiếu mã quyền chi tiết';
        }
        if (empty($tieu_de)) {
            $errors[] = 'Tiêu đề không được bỏ trống';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        $ct = QuyenChiTiet::find($id_quyen_chi_tiet);
        if (!$ct) {
            return Response::Error('Lỗi', 'Không tìm thấy quyền chi tiết');
        }

        $check = QuyenChiTiet::where('id_quyen', $ct->id_quyen)
            ->where('tieu_de', $tieu_de)
            ->where('id_quyen_chi_tiet', '!=', $id_quyen_chi_tiet)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Tiêu đề đã tồn tại trong nhóm quyền');
        }

        $count = QuyenChiTiet::where('id_quyen_chi_tiet', $id_quyen_chi_tiet)
            ->update([
                'tieu_de' => $tieu_de,
                'funcs' => $funcs,
                'show_views' => $show_views,
                'ngay_cap_nhat' => now()
            ]);

        if ($count) {
            return Response::Success('Thành công', 'Cập nhật quyền chi tiết thành công');
        }
        return Response::Error('Lỗi', 'Không tìm thấy quyền chi tiết');
    }

    public function deleteQuyenCT(Request $request)
    {
        $id_quyen_chi_tiet = $request->id_quyen_chi_tiet;
        if (!$id_quyen_chi_tiet) {
            return Response::Error('Lỗi', 'Thiếu mã quyền chi tiết');
        }

        DB::beginTransaction();
        try {
            QuyenNhomChiTiet::where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->delete();
            $check = QuyenChiTiet::where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->delete();

            if ($check) {
                DB::commit();
                return Response::Success('Thành công', 'Xóa quyền chi tiết thành công');
            }
            DB::rollBack();
            return Response::Error('Lỗi', 'Không tìm thấy quyền chi tiết');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }
}
