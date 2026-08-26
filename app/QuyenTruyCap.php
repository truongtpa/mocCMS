<?php

namespace App;

use Illuminate\Support\Facades\DB;

class QuyenTruyCap
{
    private static $cache = [];

    public static function layQuyen($id_tai_khoan)
    {
        if ($id_tai_khoan === null) {
            return ['funcs' => [], 'show_views' => []];
        }

        if (isset(self::$cache[$id_tai_khoan])) {
            return self::$cache[$id_tai_khoan];
        }

        $coNhom = DB::table('quyen_nhom_tai_khoan')
            ->where('id_tai_khoan', $id_tai_khoan)
            ->exists();

        $ds = DB::table('quyen_chi_tiet as qct')
            ->join('quyen_nhom_chi_tiet as nct', 'nct.id_quyen_chi_tiet', '=', 'qct.id_quyen_chi_tiet')
            ->when($coNhom, function ($query) use ($id_tai_khoan) {
                $query->join('quyen_nhom_tai_khoan as ntk', 'ntk.id_quyen_nhom', '=', 'nct.id_quyen_nhom')
                    ->where('ntk.id_tai_khoan', $id_tai_khoan);
            }, function ($query) {
                $query->join('quyen_nhom as qn', 'qn.id_quyen_nhom', '=', 'nct.id_quyen_nhom')
                    ->where('qn.mac_dinh', 1);
            })
            ->select('qct.funcs', 'qct.show_views')
            ->get();

        $quyen = [
            'funcs' => self::tach($ds->pluck('funcs')),
            'show_views' => self::tach($ds->pluck('show_views'))
        ];

        self::$cache[$id_tai_khoan] = $quyen;
        return $quyen;
    }

    public static function layShowViews($id_tai_khoan)
    {
        return self::layQuyen($id_tai_khoan)['show_views'];
    }

    public static function duocChay($id_tai_khoan, $ten_func)
    {
        return in_array($ten_func, self::layQuyen($id_tai_khoan)['funcs'], true);
    }

    private static function tach($cot)
    {
        return collect($cot)
            ->flatMap(fn($chuoi) => explode(',', (string)$chuoi))
            ->map(fn($item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
