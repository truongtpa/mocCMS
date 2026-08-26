<?php

namespace App\Services;

use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Services
{
    protected $s3Client;
    protected $bucket;
    protected $endpoint;

    public function __construct()
    {
        $this->bucket = config('filesystems.disks.s3.bucket');
        $this->endpoint = config('filesystems.disks.s3.endpoint');
        $this->s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'endpoint' => $this->endpoint,
            'use_path_style_endpoint' => filter_var(config('filesystems.disks.s3.use_path_style_endpoint'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Generate a pre-signed URL for an object.
     *
     * @param string $objectKey The object key (file path in the bucket).
     * @param int $expires Number of seconds until the URL expires.
     * @return string
     */
    public function taoPresignedUrl(string $objectKey, ?string $fileName = null, ?int $ttl = null): string
    {
        // TTL an toàn
        if (!$ttl) {
            $ttl = (int)env('AWS_TTL', 3600);
            if ($ttl < 1) $ttl = 3600;
            if ($ttl > 604800) $ttl = 604800;
        }

        // Client ký bằng PUBLIC endpoint để không phải replace domain
        $publicEndpoint = env('AWS_PUBLIC_ENDPOINT', env('AWS_ENDPOINT'));
        $publicUsePathStyle = filter_var(
            env('AWS_PUBLIC_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', false)),
            FILTER_VALIDATE_BOOLEAN
        );

        $signClient = new \Aws\S3\S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'endpoint' => $publicEndpoint,
            'use_path_style_endpoint' => $publicUsePathStyle,
            'signature_version' => 'v4',
        ]);

        // 1) Lấy MIME thực tế từ MinIO
        $actualMime = null;
        try {
            $head = $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $objectKey,
            ]);
            $actualMime = isset($head['ContentType']) ? (string)$head['ContentType'] : null;
        } catch (\Throwable $e) {
            // Bỏ qua, sẽ đoán theo phần mở rộng
        }

        // 2) Đoán MIME theo đuôi nếu cần
        if (!$actualMime) {
            $ext = strtolower(pathinfo($fileName ?? $objectKey, PATHINFO_EXTENSION));
            $map = [
                'pdf' => 'application/pdf',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'txt' => 'text/plain',
                'csv' => 'text/csv',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'html' => 'text/html',
                'js' => 'application/javascript',
                'css' => 'text/css',
                'mp3' => 'audio/mpeg',
                'ogg' => 'audio/ogg',
                'wav' => 'audio/wav',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'mov' => 'video/quicktime',
            ];
            $actualMime = $map[$ext] ?? 'application/octet-stream';
        }

        // 3) Quyết định xem trực tiếp hay tải về
        $isInline = false;
        if ($actualMime) {
            $isInline =
                str_starts_with($actualMime, 'image/') ||
                str_starts_with($actualMime, 'text/') ||
                str_starts_with($actualMime, 'audio/') ||
                str_starts_with($actualMime, 'video/') ||
                in_array($actualMime, [
                    'application/pdf',
                    'application/json',
                    'application/xml',
                    'image/svg+xml',
                    'application/javascript',
                ], true);
        }

        // 4) Tạo tham số ký
        $downloadName = $fileName ?: basename($objectKey);
        $params = [
            'Bucket' => $this->bucket,
            'Key' => $objectKey,
            // ép content-type để trình duyệt render đúng
            'ResponseContentType' => $actualMime,
            'ResponseContentDisposition' => ($isInline ? 'inline' : 'attachment') . '; filename="' . addslashes($downloadName) . '"',
        ];

        // 5) Ký presigned URL
        $cmd = $signClient->getCommand('GetObject', $params);
        $expiresAt = (new \DateTimeImmutable())->modify('+' . $ttl . ' seconds');
        $request = $signClient->createPresignedRequest($cmd, $expiresAt);

        return (string)$request->getUri();
    }

    /**
     * Link tải/xem tạm thời của một file: bỏ qua path rỗng, giữ nguyên URL ngoài
     * và trả về null khi không ký được thay vì ném lỗi ra ngoài.
     */
    public function linkTaiFile(?string $path, ?string $fileName = null, ?int $ttl = null): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        try {
            return $this->taoPresignedUrl($path, fileName: $fileName, ttl: $ttl);
        } catch (\Throwable $e) {
            Log::warning('Không tạo được link tải file', ['path' => $path, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /** Thư mục lưu theo ngày: [dev/]uploads/2026/08/26 */
    public static function thuMucTheoNgay(string $goc = 'uploads'): string
    {
        $prefix = app()->environment('local') ? 'dev/' : '';

        return $prefix . trim($goc, '/') . '/' . now()->format('Y/m/d');
    }

    /** Tên file lưu trên MinIO: uuid_tên-gốc.pdf */
    public static function taoTenFile(UploadedFile $file): string
    {
        return Str::uuid()->toString() . '_' . $file->getClientOriginalName();
    }

    /**
     * Upload một file lên MinIO; trả về object key hoặc null khi thất bại.
     *
     * @param string|null $thuMuc Mặc định lưu theo ngày, xem thuMucTheoNgay().
     * @param string|null $tenFile Mặc định uuid_tên-gốc, xem taoTenFile().
     */
    public static function uploadFile(UploadedFile $file, ?string $thuMuc = null, ?string $tenFile = null): ?string
    {
        if (! $file->isValid()) {
            return null;
        }

        try {
            $path = Storage::disk('minio')->putFileAs(
                $thuMuc ?? static::thuMucTheoNgay(),
                $file,
                $tenFile ?? static::taoTenFile($file)
            );

            return $path ?: null;
        } catch (\Throwable $e) {
            Log::warning('Không upload được file lên MinIO', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /** URL công khai của một object key trên MinIO. */
    public static function urlCongKhai(string $path): string
    {
        return Storage::disk('minio')->url($path);
    }

    /** File có tồn tại trên MinIO không (path rỗng hoặc URL ngoài luôn là không). */
    public static function coFile(?string $path): bool
    {
        if (empty($path) || str_starts_with($path, 'http')) {
            return false;
        }

        try {
            return Storage::disk('minio')->exists($path);
        } catch (\Throwable $e) {
            Log::warning('Không kiểm tra được file trên MinIO', ['path' => $path, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /** Xóa file trên MinIO; trả về false khi không có gì để xóa hoặc xóa thất bại. */
    public static function xoaFile(?string $path): bool
    {
        if (!static::coFile($path)) {
            return false;
        }

        try {
            return Storage::disk('minio')->delete($path);
        } catch (\Throwable $e) {
            Log::warning('Không xóa được file trên MinIO', ['path' => $path, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function getObjectContent($key)
    {
        $result = $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);
        return $result['Body']->getContents();
    }

}
