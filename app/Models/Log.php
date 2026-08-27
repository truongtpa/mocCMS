<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    protected $primaryKey = 'id_logs';

    public $timestamps = false;

    protected $fillable = ['hanh_dong', 'mo_ta', 'id_tai_khoan', 'ngay_tao'];
}
