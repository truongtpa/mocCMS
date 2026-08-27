<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaiKhoan extends Model
{
    use HasFactory;
    protected $table = 'tai_khoan';
    protected $primaryKey = 'id_tai_khoan';
    public $timestamps = false;
    public $incrementing = true;


    /** Thông tin tài khoản kèm đơn vị lấy theo chức vụ chính trong tai_khoan_chuc_vu. */
    public function thongTinCaNhan($parm)
    {
        if (empty($parm['id_tai_khoan'])) {
            return null;
        }

        return DB::table('tai_khoan')
            ->where('id_tai_khoan', $parm['id_tai_khoan'])
            ->select('id_tai_khoan', 'ho_ten', 'email')
            ->first();
    }
}
