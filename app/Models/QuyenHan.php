<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenHan extends Model
{
    use HasFactory;
    protected $table = 'quyen_han';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ma_quyen',
        'ten_quyen'
    ];
}
