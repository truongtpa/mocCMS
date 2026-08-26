<?php

namespace App\Http\Controllers;

use App\Models\QuyenChiTiet;
use App\Models\QuyenNhom;
use App\Models\QuyenNhomChiTiet;
use App\Models\QuyenNhomTaiKhoan;
use App\Models\TaiKhoanChucVu;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuyenNhomController extends Controller
{
    public function getQuyenNhom(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE', 10));
        $keyword = $request->input('s', '');

        $ds = QuyenNhom::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('tieu_de', 'like', "%{$keyword}%");
            })
            ->withCount('chiTiet')
            ->orderBy('tieu_de')
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    public function putQuyenNhom(Request $request)
    {
        $tieu_de = trim($request->tieu_de ?? '');
        $mac_dinh = intval($request->mac_dinh ?? 0);

        if (empty($tieu_de)) {
            return Response::Error('Sai định dạng dữ liệu', 'Tiêu đề không được bỏ trống');
        }

        if (QuyenNhom::where('tieu_de', $tieu_de)->exists()) {
            return Response::Error('Trùng dữ liệu', 'Tiêu đề nhóm quyền đã tồn tại');
        }

        try {
            $nhom = QuyenNhom::create([
                'tieu_de' => $tieu_de,
                'mac_dinh' => $mac_dinh,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now()
            ]);

            return Response::Success(['id_quyen_nhom' => $nhom->id_quyen_nhom], 'Thêm nhóm quyền thành công');
        } catch (\Exception $e) {
            \Log::error('Error in putQuyenNhom: ' . $e->getMessage());
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function updateQuyenNhom(Request $request)
    {
        $id_quyen_nhom = $request->id_quyen_nhom;
        $tieu_de = trim($request->tieu_de ?? '');
        $mac_dinh = intval($request->mac_dinh ?? 0);

        $errors = [];
        if (empty($id_quyen_nhom)) {
            $errors[] = 'Thiếu mã nhóm quyền';
        }
        if (empty($tieu_de)) {
            $errors[] = 'Tiêu đề không được bỏ trống';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        $check = QuyenNhom::where('tieu_de', $tieu_de)
            ->where('id_quyen_nhom', '!=', $id_quyen_nhom)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Tiêu đề nhóm quyền đã tồn tại');
        }

        $count = QuyenNhom::where('id_quyen_nhom', $id_quyen_nhom)
            ->update([
                'tieu_de' => $tieu_de,
                'mac_dinh' => $mac_dinh,
                'ngay_cap_nhat' => now()
            ]);

        if ($count) {
            return Response::Success('Thành công', 'Cập nhật nhóm quyền thành công');
        }
        return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
    }

    public function deleteQuyenNhom(Request $request)
    {
        $id_quyen_nhom = $request->id_quyen_nhom;
        if (!$id_quyen_nhom) {
            return Response::Error('Lỗi', 'Thiếu mã nhóm quyền');
        }

        DB::beginTransaction();
        try {
            QuyenNhomChiTiet::where('id_quyen_nhom', $id_quyen_nhom)->delete();
            QuyenNhomTaiKhoan::where('id_quyen_nhom', $id_quyen_nhom)->delete();
            $check = QuyenNhom::where('id_quyen_nhom', $id_quyen_nhom)->delete();

            if ($check) {
                DB::commit();
                return Response::Success('Thành công', 'Xóa nhóm quyền thành công');
            }
            DB::rollBack();
            return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in deleteQuyenNhom: ' . $e->getMessage());
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function getDsQuyenChiTiet(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE', 10));
        $keyword = $request->input('s', '');
        $id_quyen_nhom = $request->input('id_quyen_nhom');

        $ds = DB::table('quyen_chi_tiet as qct')
            ->leftJoin('quyen as q', 'q.id_quyen', '=', 'qct.id_quyen')
            ->leftJoin('quyen_nhom_chi_tiet as nct', function ($join) use ($id_quyen_nhom) {
                $join->on('nct.id_quyen_chi_tiet', '=', 'qct.id_quyen_chi_tiet')
                    ->where('nct.id_quyen_nhom', '=', $id_quyen_nhom);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('qct.tieu_de', 'like', "%{$keyword}%")
                        ->orWhere('qct.funcs', 'like', "%{$keyword}%")
                        ->orWhere('q.ten_nhom', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('q.ten_nhom')
            ->orderBy('qct.tieu_de')
            ->select(
                'qct.id_quyen_chi_tiet',
                'qct.tieu_de',
                'qct.funcs',
                'qct.show_views',
                'q.ten_nhom',
                DB::raw('IF(nct.id_quyen_nhom_chi_tiet IS NULL, 0, 1) as da_chon')
            )
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    public function dongBoQuyenNhomCT(Request $request)
    {
        $id_quyen_nhom = $request->id_quyen_nhom;
        $ids = $request->input('ids', []);

        if (!$id_quyen_nhom) {
            return Response::Error('Lỗi', 'Thiếu mã nhóm quyền');
        }

        if (!QuyenNhom::where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
        }

        $ids = collect($ids)->map(fn($id) => intval($id))->filter()->unique()->values();
        $hopLe = QuyenChiTiet::whereIn('id_quyen_chi_tiet', $ids)->pluck('id_quyen_chi_tiet');

        DB::beginTransaction();
        try {
            QuyenNhomChiTiet::where('id_quyen_nhom', $id_quyen_nhom)
                ->whereNotIn('id_quyen_chi_tiet', $hopLe)
                ->delete();

            $dangCo = QuyenNhomChiTiet::where('id_quyen_nhom', $id_quyen_nhom)
                ->pluck('id_quyen_chi_tiet');

            foreach ($hopLe->diff($dangCo) as $id_quyen_chi_tiet) {
                QuyenNhomChiTiet::create([
                    'id_quyen_nhom' => $id_quyen_nhom,
                    'id_quyen_chi_tiet' => $id_quyen_chi_tiet,
                    'ngay_tao' => now(),
                    'ngay_cap_nhat' => now()
                ]);
            }

            DB::commit();
            return Response::Success(['tong' => $hopLe->count()], 'Cập nhật quyền của nhóm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in dongBoQuyenNhomCT: ' . $e->getMessage());
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function getQuyenNhomCT(Request $request)
    {
        $keyword = $request->input('s', '');
        $id_quyen_nhom = $request->input('id_quyen_nhom');

        if (!$id_quyen_nhom) {
            return Response::Error('Lỗi', 'Thiếu mã nhóm quyền');
        }

        $ds = DB::table('quyen_nhom_chi_tiet as nct')
            ->leftJoin('quyen_chi_tiet as qct', 'qct.id_quyen_chi_tiet', '=', 'nct.id_quyen_chi_tiet')
            ->leftJoin('quyen as q', 'q.id_quyen', '=', 'qct.id_quyen')
            ->where('nct.id_quyen_nhom', $id_quyen_nhom)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('qct.tieu_de', 'like', "%{$keyword}%");
            })
            ->orderBy('q.ten_nhom')
            ->orderBy('qct.tieu_de')
            ->select(
                'nct.id_quyen_nhom_chi_tiet',
                'nct.id_quyen_nhom',
                'nct.id_quyen_chi_tiet',
                'nct.ngay_cap_nhat',
                'qct.tieu_de',
                'qct.funcs',
                'qct.show_views',
                'q.ten_nhom'
            )
            ->get()
            ->groupBy(fn($dong) => $dong->ten_nhom ?: 'Chưa có nhóm');

        $tieu_de = QuyenNhom::where('id_quyen_nhom', $id_quyen_nhom)->value('tieu_de');

        return Response::Success([
            'tieu_de' => $tieu_de,
            'nhom_quyen' => $ds
        ], '');
    }

    public function putQuyenNhomCT(Request $request)
    {
        $id_quyen_nhom = $request->id_quyen_nhom;
        $id_quyen_chi_tiet = $request->id_quyen_chi_tiet;

        $errors = [];
        if (empty($id_quyen_nhom)) {
            $errors[] = 'Thiếu mã nhóm quyền';
        }
        if (empty($id_quyen_chi_tiet)) {
            $errors[] = 'Chưa chọn quyền chi tiết';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if (!QuyenNhom::where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
        }

        if (!QuyenChiTiet::where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy quyền chi tiết');
        }

        $check = QuyenNhomChiTiet::where('id_quyen_nhom', $id_quyen_nhom)
            ->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Quyền chi tiết đã có trong nhóm quyền');
        }

        try {
            $ct = QuyenNhomChiTiet::create([
                'id_quyen_nhom' => $id_quyen_nhom,
                'id_quyen_chi_tiet' => $id_quyen_chi_tiet,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now()
            ]);

            return Response::Success(['id_quyen_nhom_chi_tiet' => $ct->id_quyen_nhom_chi_tiet], 'Thêm quyền vào nhóm thành công');
        } catch (\Exception $e) {
            \Log::error('Error in putQuyenNhomCT: ' . $e->getMessage());
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function updateQuyenNhomCT(Request $request)
    {
        $id_quyen_nhom_chi_tiet = $request->id_quyen_nhom_chi_tiet;
        $id_quyen_chi_tiet = $request->id_quyen_chi_tiet;

        $errors = [];
        if (empty($id_quyen_nhom_chi_tiet)) {
            $errors[] = 'Thiếu mã dòng quyền của nhóm';
        }
        if (empty($id_quyen_chi_tiet)) {
            $errors[] = 'Chưa chọn quyền chi tiết';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        $ct = QuyenNhomChiTiet::find($id_quyen_nhom_chi_tiet);
        if (!$ct) {
            return Response::Error('Lỗi', 'Không tìm thấy dòng quyền của nhóm');
        }

        if (!QuyenChiTiet::where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy quyền chi tiết');
        }

        $check = QuyenNhomChiTiet::where('id_quyen_nhom', $ct->id_quyen_nhom)
            ->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)
            ->where('id_quyen_nhom_chi_tiet', '!=', $id_quyen_nhom_chi_tiet)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Quyền chi tiết đã có trong nhóm quyền');
        }

        $count = QuyenNhomChiTiet::where('id_quyen_nhom_chi_tiet', $id_quyen_nhom_chi_tiet)
            ->update([
                'id_quyen_chi_tiet' => $id_quyen_chi_tiet,
                'ngay_cap_nhat' => now()
            ]);

        if ($count) {
            return Response::Success('Thành công', 'Cập nhật quyền của nhóm thành công');
        }
        return Response::Error('Lỗi', 'Không tìm thấy dòng quyền của nhóm');
    }

    public function deleteQuyenNhomCT(Request $request)
    {
        $id_quyen_nhom_chi_tiet = $request->id_quyen_nhom_chi_tiet;
        if (!$id_quyen_nhom_chi_tiet) {
            return Response::Error('Lỗi', 'Thiếu mã dòng quyền của nhóm');
        }

        $check = QuyenNhomChiTiet::where('id_quyen_nhom_chi_tiet', $id_quyen_nhom_chi_tiet)->delete();
        if ($check) {
            return Response::Success('Thành công', 'Xóa quyền khỏi nhóm thành công');
        }
        return Response::Error('Lỗi', 'Không tìm thấy dòng quyền của nhóm');
    }

    public function getQuyenNhomTK(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE', 10));
        $keyword = $request->input('s', '');
        $id_quyen_nhom = $request->input('id_quyen_nhom');

        if (!$id_quyen_nhom) {
            return Response::Error('Lỗi', 'Thiếu mã nhóm quyền');
        }

        $ds = TaiKhoanChucVu::joinChucVuChinh(
            DB::table('quyen_nhom_tai_khoan as ntk')
                ->leftJoin('tai_khoan as tk', 'tk.id_tai_khoan', '=', 'ntk.id_tai_khoan'),
            'tk', 'cv', 'dv'
        )
            ->where('ntk.id_quyen_nhom', $id_quyen_nhom)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('tk.ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('tk.email', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('tk.ho_ten')
            ->select(
                'ntk.id_quyen_nhom_tai_khoan',
                'ntk.id_quyen_nhom',
                'ntk.id_tai_khoan',
                'tk.ho_ten',
                'tk.email',
                'dv.ten_don_vi',
                'cv.ten_chuc_vu'
            )
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    public function getDsTaiKhoanChon(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE', 10));
        $keyword = $request->input('s', '');
        $id_quyen_nhom = $request->input('id_quyen_nhom');

        $ds = TaiKhoanChucVu::joinChucVuChinh(
            DB::table('tai_khoan as tk'),
            'tk', 'cv', 'dv'
        )
            ->leftJoin('quyen_nhom_tai_khoan as ntk', function ($join) use ($id_quyen_nhom) {
                $join->on('ntk.id_tai_khoan', '=', 'tk.id_tai_khoan')
                    ->where('ntk.id_quyen_nhom', '=', $id_quyen_nhom);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('tk.ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('tk.email', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('tk.ho_ten')
            ->select(
                'tk.id_tai_khoan',
                'tk.ho_ten',
                'tk.email',
                'dv.ten_don_vi',
                'cv.ten_chuc_vu',
                DB::raw('IF(ntk.id_quyen_nhom_tai_khoan IS NULL, 0, 1) as da_chon')
            )
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    public function putQuyenNhomTK(Request $request)
    {
        $id_quyen_nhom = $request->id_quyen_nhom;
        $id_tai_khoan = $request->id_tai_khoan;

        $errors = [];
        if (empty($id_quyen_nhom)) {
            $errors[] = 'Thiếu mã nhóm quyền';
        }
        if (empty($id_tai_khoan)) {
            $errors[] = 'Chưa chọn tài khoản';
        }

        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if (!QuyenNhom::where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy nhóm quyền');
        }

        if (!DB::table('tai_khoan')->where('id_tai_khoan', $id_tai_khoan)->exists()) {
            return Response::Error('Lỗi', 'Không tìm thấy tài khoản');
        }

        $check = QuyenNhomTaiKhoan::where('id_quyen_nhom', $id_quyen_nhom)
            ->where('id_tai_khoan', $id_tai_khoan)
            ->exists();
        if ($check) {
            return Response::Error('Trùng dữ liệu', 'Tài khoản đã có trong nhóm quyền');
        }

        try {
            $tk = QuyenNhomTaiKhoan::create([
                'id_quyen_nhom' => $id_quyen_nhom,
                'id_tai_khoan' => $id_tai_khoan,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now()
            ]);

            return Response::Success(['id_quyen_nhom_tai_khoan' => $tk->id_quyen_nhom_tai_khoan], 'Thêm tài khoản vào nhóm thành công');
        } catch (\Exception $e) {
            return Response::Error('Lỗi hệ thống', $e->getMessage());
        }
    }

    public function deleteQuyenNhomTK(Request $request)
    {
        $id_quyen_nhom_tai_khoan = $request->id_quyen_nhom_tai_khoan;
        if (!$id_quyen_nhom_tai_khoan) {
            return Response::Error('Lỗi', 'Thiếu mã dòng tài khoản của nhóm');
        }

        $check = QuyenNhomTaiKhoan::where('id_quyen_nhom_tai_khoan', $id_quyen_nhom_tai_khoan)->delete();
        if ($check) {
            return Response::Success('Thành công', 'Xóa tài khoản khỏi nhóm thành công');
        }
        return Response::Error('Lỗi', 'Không tìm thấy dòng tài khoản của nhóm');
    }
}
