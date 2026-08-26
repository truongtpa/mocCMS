<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenNhomChiTiet extends Model
{
    use HasFactory;
    protected $table = 'quyen_nhom_chi_tiet';
    protected $primaryKey = 'id_quyen_nhom_chi_tiet';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'id_quyen_nhom',
        'id_quyen_chi_tiet',
        'ngay_tao',
        'ngay_cap_nhat'
    ];

    public function quyenNhom()
    {
        return $this->belongsTo(QuyenNhom::class, 'id_quyen_nhom', 'id_quyen_nhom');
    }

    public function quyenChiTiet()
    {
        return $this->belongsTo(QuyenChiTiet::class, 'id_quyen_chi_tiet', 'id_quyen_chi_tiet');
    }
}
