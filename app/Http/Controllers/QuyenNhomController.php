<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuyenNhomController extends Controller
{
    public function getQuyenNhom(Request $request)
    {
        $s = trim($request->s ?? '');

        $quyenNhom = DB::table('quyen_nhom')
            ->select('id_quyen_nhom', 'ma_vai_tro', 'tieu_de', 'mac_dinh', 'ngay_tao', 'ngay_cap_nhat')
            ->selectSub(
                DB::table('quyen_nhom_chi_tiet')->selectRaw('count(*)')->whereColumn('id_quyen_nhom', 'quyen_nhom.id_quyen_nhom'),
                'so_chi_tiet'
            )
            ->selectSub(
                DB::table('quyen_nhom_tai_khoan')->selectRaw('count(*)')->whereColumn('id_quyen_nhom', 'quyen_nhom.id_quyen_nhom'),
                'so_tai_khoan'
            )
            ->orderBy('tieu_de');

        if ($s) {
            $quyenNhom->where(function ($query) use ($s) {
                $query->where('tieu_de', 'like', "%{$s}%")
                    ->orWhere('ma_vai_tro', 'like', "%{$s}%");
            });
        }

        return response()->json([
            'status' => 200,
            'data' => $quyenNhom->paginate(intval(env('ITEM_PER_PAGE', 20)))->withQueryString(),
        ]);
    }

    public function putQuyenNhom(Request $request)
    {
        $tieu_de = trim($request->tieu_de ?? '');
        $ma_vai_tro = trim($request->ma_vai_tro ?? '');
        $mac_dinh = intval($request->mac_dinh ?? 0);

        if (! $tieu_de) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề không được bỏ trống'], 400);
        }

        if (DB::table('quyen_nhom')->where('tieu_de', $tieu_de)->exists()) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề nhóm quyền đã tồn tại'], 400);
        }

        if ($ma_vai_tro && DB::table('quyen_nhom')->where('ma_vai_tro', $ma_vai_tro)->exists()) {
            return response()->json(['status' => 400, 'message' => 'Mã vai trò đã tồn tại'], 400);
        }

        $id_quyen_nhom = DB::table('quyen_nhom')->insertGetId([
            'tieu_de' => $tieu_de,
            'ma_vai_tro' => $ma_vai_tro,
            'mac_dinh' => $mac_dinh,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json([
            'status' => 200,
            'data' => ['id_quyen_nhom' => $id_quyen_nhom],
            'message' => 'Thêm nhóm quyền thành công',
        ]);
    }

    public function updateQuyenNhom(Request $request, $id_quyen_nhom)
    {
        $tieu_de = trim($request->tieu_de ?? '');
        $ma_vai_tro = trim($request->ma_vai_tro ?? '');
        $mac_dinh = intval($request->mac_dinh ?? 0);

        if (! $tieu_de) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề không được bỏ trống'], 400);
        }

        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $trung = DB::table('quyen_nhom')
            ->where('tieu_de', $tieu_de)
            ->where('id_quyen_nhom', '!=', $id_quyen_nhom)
            ->exists();

        if ($trung) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề nhóm quyền đã tồn tại'], 400);
        }

        $trungMa = $ma_vai_tro && DB::table('quyen_nhom')
            ->where('ma_vai_tro', $ma_vai_tro)
            ->where('id_quyen_nhom', '!=', $id_quyen_nhom)
            ->exists();

        if ($trungMa) {
            return response()->json(['status' => 400, 'message' => 'Mã vai trò đã tồn tại'], 400);
        }

        DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->update([
            'tieu_de' => $tieu_de,
            'ma_vai_tro' => $ma_vai_tro,
            'mac_dinh' => $mac_dinh,
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Cập nhật nhóm quyền thành công']);
    }

    public function deleteQuyenNhom($id_quyen_nhom)
    {
        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        DB::transaction(function () use ($id_quyen_nhom) {
            DB::table('quyen_nhom_chi_tiet')->where('id_quyen_nhom', $id_quyen_nhom)->delete();
            DB::table('quyen_nhom_tai_khoan')->where('id_quyen_nhom', $id_quyen_nhom)->delete();
            DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->delete();
        });

        return response()->json(['status' => 200, 'message' => 'Xóa nhóm quyền thành công']);
    }

    public function getQuyenNhomCT(Request $request, $id_quyen_nhom)
    {
        $quyenNhom = DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->first();

        if (! $quyenNhom) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $s = trim($request->s ?? '');

        $chiTiet = DB::table('quyen_nhom_chi_tiet as nct')
            ->leftJoin('quyen_chi_tiet as qct', 'qct.id_quyen_chi_tiet', '=', 'nct.id_quyen_chi_tiet')
            ->leftJoin('quyen as q', 'q.id_quyen', '=', 'qct.id_quyen')
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
            ->where('nct.id_quyen_nhom', $id_quyen_nhom)
            ->orderBy('q.ten_nhom')
            ->orderBy('qct.tieu_de');

        if ($s) {
            $chiTiet->where('qct.tieu_de', 'like', "%{$s}%");
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'tieu_de' => $quyenNhom->tieu_de,
                'danh_sach' => $chiTiet->get()->map(function ($dong) {
                    $dong->ten_nhom = $dong->ten_nhom ?: 'Chưa có nhóm';

                    return $dong;
                })->values(),
            ],
        ]);
    }

    public function putQuyenNhomCT(Request $request, $id_quyen_nhom)
    {
        $id_quyen_chi_tiet = intval($request->id_quyen_chi_tiet ?? 0);

        if (! $id_quyen_chi_tiet) {
            return response()->json(['status' => 400, 'message' => 'Chưa chọn quyền chi tiết'], 400);
        }

        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        if (! DB::table('quyen_chi_tiet')->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy quyền chi tiết'], 404);
        }

        $trung = DB::table('quyen_nhom_chi_tiet')
            ->where('id_quyen_nhom', $id_quyen_nhom)
            ->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)
            ->exists();

        if ($trung) {
            return response()->json(['status' => 400, 'message' => 'Quyền chi tiết đã có trong nhóm quyền'], 400);
        }

        $id_quyen_nhom_chi_tiet = DB::table('quyen_nhom_chi_tiet')->insertGetId([
            'id_quyen_nhom' => $id_quyen_nhom,
            'id_quyen_chi_tiet' => $id_quyen_chi_tiet,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json([
            'status' => 200,
            'data' => ['id_quyen_nhom_chi_tiet' => $id_quyen_nhom_chi_tiet],
            'message' => 'Thêm quyền vào nhóm thành công',
        ]);
    }

    public function deleteQuyenNhomCT($id_quyen_nhom, $id_quyen_nhom_chi_tiet)
    {
        $tonTai = DB::table('quyen_nhom_chi_tiet')
            ->where('id_quyen_nhom_chi_tiet', $id_quyen_nhom_chi_tiet)
            ->where('id_quyen_nhom', $id_quyen_nhom)
            ->exists();

        if (! $tonTai) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy dòng quyền của nhóm'], 404);
        }

        DB::table('quyen_nhom_chi_tiet')->where('id_quyen_nhom_chi_tiet', $id_quyen_nhom_chi_tiet)->delete();

        return response()->json(['status' => 200, 'message' => 'Xóa quyền khỏi nhóm thành công']);
    }

    public function getDsQuyenChiTiet(Request $request, $id_quyen_nhom)
    {
        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $s = trim($request->s ?? '');

        $dsQuyen = DB::table('quyen_chi_tiet as qct')
            ->leftJoin('quyen as q', 'q.id_quyen', '=', 'qct.id_quyen')
            ->leftJoin('quyen_nhom_chi_tiet as nct', function ($join) use ($id_quyen_nhom) {
                $join->on('nct.id_quyen_chi_tiet', '=', 'qct.id_quyen_chi_tiet')
                    ->where('nct.id_quyen_nhom', '=', $id_quyen_nhom);
            })
            ->select(
                'qct.id_quyen_chi_tiet',
                'qct.tieu_de',
                'qct.funcs',
                'qct.show_views',
                'q.ten_nhom',
                DB::raw('if(nct.id_quyen_nhom_chi_tiet is null, 0, 1) as da_chon')
            )
            ->orderBy('q.ten_nhom')
            ->orderBy('qct.tieu_de');

        if ($s) {
            $dsQuyen->where(function ($query) use ($s) {
                $query->where('qct.tieu_de', 'like', "%{$s}%")
                    ->orWhere('qct.funcs', 'like', "%{$s}%")
                    ->orWhere('q.ten_nhom', 'like', "%{$s}%");
            });
        }

        return response()->json([
            'status' => 200,
            'data' => $dsQuyen->paginate(intval(env('ITEM_PER_PAGE', 20)))->withQueryString(),
        ]);
    }

    public function updateDsQuyenChiTiet(Request $request, $id_quyen_nhom)
    {
        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $ids = collect($request->input('ids', []))->map(fn ($id) => intval($id))->filter()->unique()->values();
        $hopLe = DB::table('quyen_chi_tiet')->whereIn('id_quyen_chi_tiet', $ids)->pluck('id_quyen_chi_tiet');

        DB::transaction(function () use ($id_quyen_nhom, $hopLe) {
            DB::table('quyen_nhom_chi_tiet')
                ->where('id_quyen_nhom', $id_quyen_nhom)
                ->whereNotIn('id_quyen_chi_tiet', $hopLe)
                ->delete();

            $dangCo = DB::table('quyen_nhom_chi_tiet')
                ->where('id_quyen_nhom', $id_quyen_nhom)
                ->pluck('id_quyen_chi_tiet');

            $themMoi = $hopLe->diff($dangCo)->map(fn ($id_quyen_chi_tiet) => [
                'id_quyen_nhom' => $id_quyen_nhom,
                'id_quyen_chi_tiet' => $id_quyen_chi_tiet,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now(),
            ])->all();

            if ($themMoi) {
                DB::table('quyen_nhom_chi_tiet')->insert($themMoi);
            }
        });

        return response()->json([
            'status' => 200,
            'data' => ['tong' => $hopLe->count()],
            'message' => 'Cập nhật quyền của nhóm thành công',
        ]);
    }

    public function getQuyenNhomTK(Request $request, $id_quyen_nhom)
    {
        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $s = trim($request->s ?? '');

        $dsTaiKhoan = DB::table('quyen_nhom_tai_khoan as ntk')
            ->leftJoin('tai_khoan as tk', 'tk.id_tai_khoan', '=', 'ntk.id_tai_khoan')
            ->select(
                'ntk.id_quyen_nhom_tai_khoan',
                'ntk.id_quyen_nhom',
                'ntk.id_tai_khoan',
                'tk.ho_ten',
                'tk.email',
                'tk.trang_thai'
            )
            ->where('ntk.id_quyen_nhom', $id_quyen_nhom)
            ->orderBy('tk.ho_ten');

        if ($s) {
            $dsTaiKhoan->where(function ($query) use ($s) {
                $query->where('tk.ho_ten', 'like', "%{$s}%")
                    ->orWhere('tk.email', 'like', "%{$s}%");
            });
        }

        return response()->json([
            'status' => 200,
            'data' => $dsTaiKhoan->paginate(intval(env('ITEM_PER_PAGE', 20)))->withQueryString(),
        ]);
    }

    public function getDsTaiKhoanChon(Request $request, $id_quyen_nhom)
    {
        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $s = trim($request->s ?? '');

        $dsTaiKhoan = DB::table('tai_khoan as tk')
            ->leftJoin('quyen_nhom_tai_khoan as ntk', function ($join) use ($id_quyen_nhom) {
                $join->on('ntk.id_tai_khoan', '=', 'tk.id_tai_khoan')
                    ->where('ntk.id_quyen_nhom', '=', $id_quyen_nhom);
            })
            ->select(
                'tk.id_tai_khoan',
                'tk.ho_ten',
                'tk.email',
                'tk.trang_thai',
                DB::raw('if(ntk.id_quyen_nhom_tai_khoan is null, 0, 1) as da_chon')
            )
            ->orderBy('tk.ho_ten');

        if ($s) {
            $dsTaiKhoan->where(function ($query) use ($s) {
                $query->where('tk.ho_ten', 'like', "%{$s}%")
                    ->orWhere('tk.email', 'like', "%{$s}%");
            });
        }

        return response()->json([
            'status' => 200,
            'data' => $dsTaiKhoan->paginate(intval(env('ITEM_PER_PAGE', 20)))->withQueryString(),
        ]);
    }

    public function putQuyenNhomTK(Request $request, $id_quyen_nhom)
    {
        $id_tai_khoan = intval($request->id_tai_khoan ?? 0);

        if (! $id_tai_khoan) {
            return response()->json(['status' => 400, 'message' => 'Chưa chọn tài khoản'], 400);
        }

        if (! DB::table('quyen_nhom')->where('id_quyen_nhom', $id_quyen_nhom)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        if (! DB::table('tai_khoan')->where('id_tai_khoan', $id_tai_khoan)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy tài khoản'], 404);
        }

        $trung = DB::table('quyen_nhom_tai_khoan')
            ->where('id_quyen_nhom', $id_quyen_nhom)
            ->where('id_tai_khoan', $id_tai_khoan)
            ->exists();

        if ($trung) {
            return response()->json(['status' => 400, 'message' => 'Tài khoản đã có trong nhóm quyền'], 400);
        }

        $id_quyen_nhom_tai_khoan = DB::table('quyen_nhom_tai_khoan')->insertGetId([
            'id_quyen_nhom' => $id_quyen_nhom,
            'id_tai_khoan' => $id_tai_khoan,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json([
            'status' => 200,
            'data' => ['id_quyen_nhom_tai_khoan' => $id_quyen_nhom_tai_khoan],
            'message' => 'Thêm tài khoản vào nhóm thành công',
        ]);
    }

    public function deleteQuyenNhomTK($id_quyen_nhom, $id_quyen_nhom_tai_khoan)
    {
        $tonTai = DB::table('quyen_nhom_tai_khoan')
            ->where('id_quyen_nhom_tai_khoan', $id_quyen_nhom_tai_khoan)
            ->where('id_quyen_nhom', $id_quyen_nhom)
            ->exists();

        if (! $tonTai) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy dòng tài khoản của nhóm'], 404);
        }

        DB::table('quyen_nhom_tai_khoan')->where('id_quyen_nhom_tai_khoan', $id_quyen_nhom_tai_khoan)->delete();

        return response()->json(['status' => 200, 'message' => 'Xóa tài khoản khỏi nhóm thành công']);
    }
}
