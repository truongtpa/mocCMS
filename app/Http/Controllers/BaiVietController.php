<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaiVietController extends Controller
{
    /** Chỉ cho phép sắp xếp theo các cột này để tránh SQL injection qua tên cột */
    private const COT_SAP_XEP = ['tieu_de', 'xuat_ban', 'ngay_tao'];

    public function getBaiViet(Request $request)
    {
        $s = trim($request->s ?? '');

        $sort = in_array($request->sort, self::COT_SAP_XEP, true) ? $request->sort : 'ngay_tao';
        $order = strtolower($request->order ?? '') === 'asc' ? 'asc' : 'desc';

        $baiViet = DB::table('tin_tuc')
            ->select('id_tin_tuc', 'tieu_de', 'noi_dung', 'thumbnail', 'url', 'xuat_ban', 'ngay_tao')
            ->orderBy($sort, $order);

        if ($s) {
            $baiViet->where('tieu_de', 'like', "%{$s}%");
        }

        return response()->json([
            'status' => 200,
            'data' => $baiViet->paginate(10)->withQueryString(),
        ]);
    }
}
