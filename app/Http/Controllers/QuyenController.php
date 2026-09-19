<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuyenController extends Controller
{
    public function getQuyen(Request $request)
    {
        $s = trim($request->s ?? '');

        $quyen = DB::table('quyen')
            ->select('id_quyen', 'ten_nhom', 'ngay_tao', 'ngay_cap_nhat')
            ->selectSub(
                DB::table('quyen_chi_tiet')->selectRaw('count(*)')->whereColumn('id_quyen', 'quyen.id_quyen'),
                'so_chi_tiet'
            )
            ->orderBy('ten_nhom');

        if ($s) {
            $quyen->where('ten_nhom', 'like', "%{$s}%");
        }

        return response()->json([
            'status' => 200,
            'data' => $quyen->paginate(intval(env('ITEM_PER_PAGE', 20)))->withQueryString(),
        ]);
    }

    public function putQuyen(Request $request)
    {
        $ten_nhom = trim($request->ten_nhom ?? '');

        if (! $ten_nhom) {
            return response()->json(['status' => 400, 'message' => 'Tên nhóm quyền không được bỏ trống'], 400);
        }

        if (DB::table('quyen')->where('ten_nhom', $ten_nhom)->exists()) {
            return response()->json(['status' => 400, 'message' => 'Tên nhóm quyền đã tồn tại'], 400);
        }

        $id_quyen = DB::table('quyen')->insertGetId([
            'ten_nhom' => $ten_nhom,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json([
            'status' => 200,
            'data' => ['id_quyen' => $id_quyen],
            'message' => 'Thêm nhóm quyền thành công',
        ]);
    }

    public function updateQuyen(Request $request, $id_quyen)
    {
        $ten_nhom = trim($request->ten_nhom ?? '');

        if (! $ten_nhom) {
            return response()->json(['status' => 400, 'message' => 'Tên nhóm quyền không được bỏ trống'], 400);
        }

        if (! DB::table('quyen')->where('id_quyen', $id_quyen)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $trung = DB::table('quyen')
            ->where('ten_nhom', $ten_nhom)
            ->where('id_quyen', '!=', $id_quyen)
            ->exists();

        if ($trung) {
            return response()->json(['status' => 400, 'message' => 'Tên nhóm quyền đã tồn tại'], 400);
        }

        DB::table('quyen')->where('id_quyen', $id_quyen)->update([
            'ten_nhom' => $ten_nhom,
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Cập nhật nhóm quyền thành công']);
    }

    public function deleteQuyen($id_quyen)
    {
        if (! DB::table('quyen')->where('id_quyen', $id_quyen)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        DB::transaction(function () use ($id_quyen) {
            $dsChiTiet = DB::table('quyen_chi_tiet')->where('id_quyen', $id_quyen)->pluck('id_quyen_chi_tiet');

            DB::table('quyen_nhom_chi_tiet')->whereIn('id_quyen_chi_tiet', $dsChiTiet)->delete();
            DB::table('quyen_chi_tiet')->where('id_quyen', $id_quyen)->delete();
            DB::table('quyen')->where('id_quyen', $id_quyen)->delete();
        });

        return response()->json(['status' => 200, 'message' => 'Xóa nhóm quyền thành công']);
    }

    public function getQuyenCT(Request $request, $id_quyen)
    {
        if (! DB::table('quyen')->where('id_quyen', $id_quyen)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $s = trim($request->s ?? '');

        $chiTiet = DB::table('quyen_chi_tiet')
            ->select('id_quyen_chi_tiet', 'id_quyen', 'tieu_de', 'funcs', 'show_views', 'ngay_tao', 'ngay_cap_nhat')
            ->where('id_quyen', $id_quyen)
            ->orderBy('tieu_de');

        if ($s) {
            $chiTiet->where('tieu_de', 'like', "%{$s}%");
        }

        return response()->json([
            'status' => 200,
            'data' => $chiTiet->paginate(intval(env('ITEM_PER_PAGE', 20)))->withQueryString(),
        ]);
    }

    public function putQuyenCT(Request $request, $id_quyen)
    {
        $tieu_de = trim($request->tieu_de ?? '');
        $funcs = trim($request->funcs ?? '');
        $show_views = trim($request->show_views ?? '');

        if (! $tieu_de) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề không được bỏ trống'], 400);
        }

        if (! DB::table('quyen')->where('id_quyen', $id_quyen)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy nhóm quyền'], 404);
        }

        $trung = DB::table('quyen_chi_tiet')
            ->where('id_quyen', $id_quyen)
            ->where('tieu_de', $tieu_de)
            ->exists();

        if ($trung) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề đã tồn tại trong nhóm quyền'], 400);
        }

        $id_quyen_chi_tiet = DB::table('quyen_chi_tiet')->insertGetId([
            'id_quyen' => $id_quyen,
            'tieu_de' => $tieu_de,
            'funcs' => $funcs,
            'show_views' => $show_views,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json([
            'status' => 200,
            'data' => ['id_quyen_chi_tiet' => $id_quyen_chi_tiet],
            'message' => 'Thêm quyền chi tiết thành công',
        ]);
    }

    public function updateQuyenCT(Request $request, $id_quyen, $id_quyen_chi_tiet)
    {
        $tieu_de = trim($request->tieu_de ?? '');
        $funcs = trim($request->funcs ?? '');
        $show_views = trim($request->show_views ?? '');

        if (! $tieu_de) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề không được bỏ trống'], 400);
        }

        $chiTiet = DB::table('quyen_chi_tiet')
            ->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)
            ->where('id_quyen', $id_quyen)
            ->first();

        if (! $chiTiet) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy quyền chi tiết'], 404);
        }

        $trung = DB::table('quyen_chi_tiet')
            ->where('id_quyen', $id_quyen)
            ->where('tieu_de', $tieu_de)
            ->where('id_quyen_chi_tiet', '!=', $id_quyen_chi_tiet)
            ->exists();

        if ($trung) {
            return response()->json(['status' => 400, 'message' => 'Tiêu đề đã tồn tại trong nhóm quyền'], 400);
        }

        DB::table('quyen_chi_tiet')->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->update([
            'tieu_de' => $tieu_de,
            'funcs' => $funcs,
            'show_views' => $show_views,
            'ngay_cap_nhat' => now(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Cập nhật quyền chi tiết thành công']);
    }

    public function deleteQuyenCT($id_quyen, $id_quyen_chi_tiet)
    {
        $tonTai = DB::table('quyen_chi_tiet')
            ->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)
            ->where('id_quyen', $id_quyen)
            ->exists();

        if (! $tonTai) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy quyền chi tiết'], 404);
        }

        DB::transaction(function () use ($id_quyen_chi_tiet) {
            DB::table('quyen_nhom_chi_tiet')->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->delete();
            DB::table('quyen_chi_tiet')->where('id_quyen_chi_tiet', $id_quyen_chi_tiet)->delete();
        });

        return response()->json(['status' => 200, 'message' => 'Xóa quyền chi tiết thành công']);
    }
}
