<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenNhomTaiKhoan extends Model
{
    use HasFactory;
    protected $table = 'quyen_nhom_tai_khoan';
    protected $primaryKey = 'id_quyen_nhom_tai_khoan';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'id_quyen_nhom',
        'id_tai_khoan',
        'ngay_tao',
        'ngay_cap_nhat'
    ];

    public function quyenNhom()
    {
        return $this->belongsTo(QuyenNhom::class, 'id_quyen_nhom', 'id_quyen_nhom');
    }
}
