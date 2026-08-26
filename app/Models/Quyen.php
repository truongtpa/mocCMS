<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quyen extends Model
{
    use HasFactory;
    protected $table = 'quyen';
    protected $primaryKey = 'id_quyen';
    public $timestamps = false;
    public $incrementing = true;

    /**
     * Các trường được phép gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'ten_nhom',
        'ngay_tao',
        'ngay_cap_nhat'
    ];

    /**
     * Danh sách quyền chi tiết thuộc nhóm quyền.
     */
    public function chiTiet()
    {
        return $this->hasMany(QuyenChiTiet::class, 'id_quyen', 'id_quyen');
    }
}
