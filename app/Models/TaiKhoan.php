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
        return TaiKhoanChucVu::joinChucVuChinh(DB::table('tai_khoan'), 'tai_khoan', null, 'don_vi')
            ->where('tai_khoan.id_tai_khoan', $parm['id_tai_khoan'])
            ->select(
                'tai_khoan.id_tai_khoan', 'tai_khoan.ho_ten', 'tai_khoan.email',
                'tkcv_chinh.id_don_vi', 'don_vi.ten_don_vi'
            )
            ->first();
    }
}
