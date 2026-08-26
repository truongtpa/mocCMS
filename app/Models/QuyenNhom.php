<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenNhom extends Model
{
    use HasFactory;
    protected $table = 'quyen_nhom';
    protected $primaryKey = 'id_quyen_nhom';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'tieu_de',
        'mac_dinh',
        'ngay_tao',
        'ngay_cap_nhat'
    ];

    public function chiTiet()
    {
        return $this->hasMany(QuyenNhomChiTiet::class, 'id_quyen_nhom', 'id_quyen_nhom');
    }
}
