<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenChiTiet extends Model
{
    use HasFactory;
    protected $table = 'quyen_chi_tiet';
    protected $primaryKey = 'id_quyen_chi_tiet';
    public $timestamps = false;
    public $incrementing = true;

    /**
     * Các trường được phép gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'id_quyen',
        'tieu_de',
        'funcs',
        'show_views',
        'ngay_tao',
        'ngay_cap_nhat'
    ];

    /**
     * Nhóm quyền cha.
     */
    public function quyen()
    {
        return $this->belongsTo(Quyen::class, 'id_quyen', 'id_quyen');
    }
}
