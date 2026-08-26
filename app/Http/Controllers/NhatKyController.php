<?php

namespace App\Http\Controllers;

use App\Models\NhatKy;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhatKyController extends Controller
{
    public function getDanhSach(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE')) ?: 20;
        $keyword = trim((string) $request->input('s', ''));
        $action = trim((string) $request->input('action', ''));
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $ds = NhatKy::query()
            ->leftJoin('tai_khoan', 'tai_khoan.id_tai_khoan', '=', 'logs.id_tai_khoan')
            ->when($keyword, function ($query) use ($keyword) {
                // Tìm theo cả tên người dùng lẫn tên hành động.
                $query->where(function ($q) use ($keyword) {
                    $q->where('logs.action', 'like', "%{$keyword}%")
                        ->orWhere('tai_khoan.ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('tai_khoan.email', 'like', "%{$keyword}%");
                });
            })
            ->when($action, fn ($query) => $query->where('logs.action', $action))
            ->when($tuNgay, fn ($query) => $query->whereDate('logs.ngay_tao', '>=', $tuNgay))
            ->when($denNgay, fn ($query) => $query->whereDate('logs.ngay_tao', '<=', $denNgay))
            ->orderByDesc('logs.ngay_tao')
            ->orderByDesc('logs.id_logs')
            ->select('logs.*', 'tai_khoan.ho_ten', 'tai_khoan.email')
            ->paginate($perPage);

        return Response::Success($ds, '');
    }

    /** Danh sách hành động đã phát sinh, dùng đổ vào ô lọc. */
    public function getDsHanhDong(Request $request)
    {
        $ds = DB::table('logs')
            ->select('action', DB::raw('count(*) as so_lan'))
            ->groupBy('action')
            ->orderBy('action')
            ->get()
            ->map(fn ($d) => [
                'action' => $d->action,
                'mo_ta' => NhatKy::moTa($d->action),
                'so_lan' => $d->so_lan,
            ]);

        return Response::Success($ds, '');
    }
}
