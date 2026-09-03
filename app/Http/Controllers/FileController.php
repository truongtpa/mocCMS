<?php

namespace App\Http\Controllers;

use App\Response;
use App\Services\S3Services;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function putUploadFile(Request $request)
    {
        $file = $request->file('file') ?? $request->file('upload');
        if (!$file || !$file->isValid()) {
            return Response::Error('Sai định dạng dữ liệu', ['file' => 'Tệp tải lên không hợp lệ']);
        }

        $thuMuc = $request->input('folder', 'uploads');
        $path = S3Services::uploadFile($file, $thuMuc);
        if (!$path) {
            return Response::Error('Lỗi hệ thống', 'Không thể lưu tệp');
        }

        $url = S3Services::urlCongKhai($path);

        return Response::Success([
            'path' => $path,
            'url' => $url,
            'ten_goc' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getClientMimeType()
        ], 'Tải tệp thành công');
    }

    public function putUploadHangLoat(Request $request)
    {
        $files = $request->file('files') ?? $request->file('uploads');
        if (!is_array($files) || count($files) === 0) {
            return Response::Error('Sai định dạng dữ liệu', ['files' => 'Danh sách tệp không được bỏ trống']);
        }

        $thuMuc = $request->input('folder', 'uploads');
        $danhSach = S3Services::uploadHangLoat($files, $thuMuc);

        return Response::Success($danhSach, 'Tải danh sách tệp thành công');
    }

    public function deleteFile(Request $request)
    {
        $path = $request->input('path');
        $paths = $request->input('paths');

        if (empty($path) && (empty($paths) || !is_array($paths))) {
            return Response::Error('Sai định dạng dữ liệu', ['path' => 'Đường dẫn tệp không được bỏ trống']);
        }

        if ($paths && is_array($paths)) {
            $soLuong = S3Services::xoaHangLoat($paths);
            return Response::Success(['so_luong' => $soLuong], 'Xóa các tệp thành công');
        }

        $isDeleted = S3Services::xoaFile($path);
        if (!$isDeleted) {
            return Response::Error('Lỗi', 'Không thể xóa tệp hoặc tệp không tồn tại');
        }

        return Response::Success(null, 'Xóa tệp thành công');
    }

    public function getFileUrl(Request $request)
    {
        $path = $request->input('path');
        if (empty($path)) {
            return Response::Error('Sai định dạng dữ liệu', ['path' => 'Đường dẫn tệp không được bỏ trống']);
        }

        $url = S3Services::urlCongKhai($path);
        return Response::Success(['url' => $url, 'path' => $path], 'Lấy đường dẫn thành công');
    }
}
