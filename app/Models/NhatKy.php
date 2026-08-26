<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class NhatKy extends Model
{
    protected $table = 'logs';

    protected $primaryKey = 'id_logs';

    public $timestamps = false;

    protected $fillable = ['id_tai_khoan', 'action', 'parms', 'ngay_tao'];

    public const TRUONG_NHAY_CAM = [];

    protected $appends = ['mo_ta'];

    public const MO_TA = [
        'TrinhKyController.postTrinhKy' => 'Tạo trình ký',
        'TrinhKyController.putTrinhKy' => 'Cập nhật trình ký',
        'TrinhKyController.deleteTrinhKy' => 'Xóa trình ký',
        'TrinhKyController.postPheDuyet' => 'Phê duyệt trình ký',
        'UploadFileController.upload' => 'Tải file lên',
        'UploadFileController.revert' => 'Hủy file vừa tải lên',
    ];

    public static function ghi(?int $id_tai_khoan, string $action, ?string $parms = null): void
    {
        try {
            static::create([
                'id_tai_khoan' => $id_tai_khoan,
                'action' => $action,
                'parms' => $parms ?? static::thamSoHienTai(),
                'ngay_tao' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Không ghi được nhật ký', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }


    public static function thamSoHienTai(): string
    {
        try {
            $duLieu = static::locGiaTri(request()->except(static::TRUONG_NHAY_CAM));

            foreach (static::TRUONG_NHAY_CAM as $truong) {
                if (request()->has($truong)) {
                    $duLieu[$truong] = '***';
                }
            }

            $route = request()->route();
            if ($route) {
                $duLieu = array_merge(static::locGiaTri($route->parameters()), $duLieu);
            }

            if (empty($duLieu)) {
                return '{}';
            }

            $json = json_encode($duLieu, JSON_UNESCAPED_UNICODE) ?: '{}';

            return mb_strlen($json) > 5000 ? mb_substr($json, 0, 5000).'…' : $json;
        } catch (\Throwable $e) {
            return '{}';
        }
    }

    private static function locGiaTri($giaTri)
    {
        if ($giaTri instanceof UploadedFile) {
            return '[file] '.$giaTri->getClientOriginalName();
        }

        if (is_array($giaTri)) {
            return array_map(fn ($item) => static::locGiaTri($item), $giaTri);
        }

        if ($giaTri instanceof Model) {
            return $giaTri->getKey();
        }

        return $giaTri;
    }

    public static function moTa(string $action): string
    {
        return static::MO_TA[$action] ?? $action;
    }

    public function getMoTaAttribute(): string
    {
        return static::moTa($this->action);
    }
}
