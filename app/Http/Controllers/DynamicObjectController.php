<?php

namespace App\Http\Controllers;

use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicObjectController extends Controller
{
    private function checkUserPermission($permissionKey)
    {
        return \App\VLUTE::checkPermission($permissionKey);
    }

    public function getTypes()
    {
        if (!$this->checkUserPermission('DynamicObjectController.getTypes')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $types = DB::table('loai_doi_tuong')->select('loai_doi_tuong.*')->get();

        foreach ($types as $type) {
            $type->fields_count = DB::table('danh_muc_truong')
                ->where('loai_doi_tuong_id', $type->id)
                ->count();

            if ($type->ma_loai === 'giang_vien') {
                $type->records_count = DB::table('giang_vien')->count();
            } elseif ($type->ma_loai === 'sinh_vien') {
                $type->records_count = DB::table('sinh_vien')->count();
            } else {
                $type->records_count = DB::table('doi_tuong')
                    ->where('loai_doi_tuong_id', $type->id)
                    ->count();
            }
        }

        return Response::Success($types, 'Lấy danh sách loại đối tượng thành công');
    }

    public function putType(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.putType')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $maLoai = trim($request->input('ma_loai', ''));
        $tenLoai = trim($request->input('ten_loai', ''));
        $moTa = trim($request->input('mo_ta', ''));

        $errors = [];
        if (empty($maLoai)) $errors[] = 'Mã loại đối tượng không được bỏ trống';
        if (empty($tenLoai)) $errors[] = 'Tên loại đối tượng không được bỏ trống';
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if ($id) {
            DB::table('loai_doi_tuong')->where('id', $id)->update([
                'ma_loai' => $maLoai,
                'ten_loai' => $tenLoai,
                'mo_ta' => $moTa,
            ]);
            $msg = 'Cập nhật loại đối tượng thành công!';
        } else {
            $exists = DB::table('loai_doi_tuong')->where('ma_loai', $maLoai)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Mã loại đối tượng này đã tồn tại!');
            }

            DB::table('loai_doi_tuong')->insert([
                'ma_loai' => $maLoai,
                'ten_loai' => $tenLoai,
                'mo_ta' => $moTa,
                'ngay_tao' => now(),
            ]);
            $msg = 'Thêm loại đối tượng thành công!';
        }

        return Response::Success([], $msg);
    }

    public function deleteType($id)
    {
        if (!$this->checkUserPermission('DynamicObjectController.deleteType')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $type = DB::table('loai_doi_tuong')->where('id', $id)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        if (in_array($type->ma_loai, ['giang_vien', 'sinh_vien'])) {
            return Response::Error('Lỗi thao tác', 'Không thể xóa loại đối tượng mặc định của hệ thống!');
        }

        DB::table('loai_doi_tuong')->where('id', $id)->delete();

        return Response::Success([], 'Xóa loại đối tượng thành công!');
    }

    public function getFields(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.getFields')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        if (!$loaiDoiTuongId) {
            return Response::Error('Thiếu dữ liệu', 'Thiếu loai_doi_tuong_id!');
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->orderBy('thu_tu', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($f) {
                $f->ref_options = self::resolveAttributeOptions($f);
                return $f;
            });

        return Response::Success($fields, 'Lấy danh sách thuộc tính thành công');
    }

    public function putField(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.putField')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');
        $maTruong = trim($request->input('ma_truong', ''));
        $tenTruong = trim($request->input('ten_truong', ''));
        $phanNhom = trim($request->input('phan_nhom', 'Thông tin bổ sung'));
        $kieuDuLieu = $request->input('kieu_du_lieu', 'text');
        $trangThai = $request->input('trang_thai', true);
        $thuTu = intval($request->input('thu_tu', 0));

        $errors = [];
        if (!$loaiDoiTuongId) $errors[] = 'Loại đối tượng không được bỏ trống';
        if (empty($maTruong)) $errors[] = 'Mã thuộc tính không được bỏ trống';
        if (empty($tenTruong)) $errors[] = 'Tên thuộc tính không được bỏ trống';
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        $lienKetLoaiDoiTuongId = $request->input('lien_ket_loai_doi_tuong_id');
        $luaChon = $request->input('lua_chon');
        $batBuoc = $request->input('bat_buoc', false);
        $choPhepChinhSua = $request->input('cho_phep_chinh_sua', true);
        $cauHinh = $request->input('cau_hinh');

        $data = [
            'ma_truong' => $maTruong,
            'ten_truong' => $tenTruong,
            'phan_nhom' => empty($phanNhom) ? 'Thông tin bổ sung' : $phanNhom,
            'kieu_du_lieu' => $kieuDuLieu,
            'trang_thai' => $trangThai,
            'thu_tu' => $thuTu,
            'bat_buoc' => (bool)$batBuoc,
            'cho_phep_chinh_sua' => (bool)$choPhepChinhSua,
            'lien_ket_loai_doi_tuong_id' => $lienKetLoaiDoiTuongId ? intval($lienKetLoaiDoiTuongId) : null,
            'lua_chon' => is_array($luaChon) ? json_encode($luaChon, JSON_UNESCAPED_UNICODE) : $luaChon,
            'cau_hinh' => is_array($cauHinh) ? json_encode($cauHinh, JSON_UNESCAPED_UNICODE) : $cauHinh,
        ];

        if ($id) {
            DB::table('danh_muc_truong')->where('id', $id)->update($data);
        } else {
            $exists = DB::table('danh_muc_truong')
                ->where('loai_doi_tuong_id', $loaiDoiTuongId)
                ->where('ma_truong', $maTruong)
                ->exists();

            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Mã thuộc tính đã tồn tại trong loại đối tượng này!');
            }

            $data['loai_doi_tuong_id'] = $loaiDoiTuongId;
            $data['ngay_tao'] = now();
            DB::table('danh_muc_truong')->insert($data);
        }

        return Response::Success([], 'Lưu thuộc tính thành công!');
    }

    public function updateFieldOrders(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.updateFieldOrders')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $orders = $request->input('orders', []);
        if (!is_array($orders)) {
            return Response::Error('Sai định dạng dữ liệu', 'orders phải là mảng!');
        }

        foreach ($orders as $item) {
            if (isset($item['id'])) {
                $updateData = [];
                if (isset($item['thu_tu'])) {
                    $updateData['thu_tu'] = intval($item['thu_tu']);
                }
                if (isset($item['phan_nhom']) && !empty($item['phan_nhom'])) {
                    $updateData['phan_nhom'] = trim($item['phan_nhom']);
                }
                if (!empty($updateData)) {
                    DB::table('danh_muc_truong')->where('id', $item['id'])->update($updateData);
                }
            }
        }
        return Response::Success([], 'Cập nhật thứ tự sắp xếp và phân nhóm thành công!');
    }

    public function deleteField($id)
    {
        if (!$this->checkUserPermission('DynamicObjectController.deleteField')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $deleted = DB::table('danh_muc_truong')->where('id', $id)->delete();
        if ($deleted === 0) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy thuộc tính!');
        }

        return Response::Success([], 'Xóa thuộc tính thành công!');
    }

    public function getRecords(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.getRecords')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        if (!$loaiDoiTuongId) {
            return Response::Error('Thiếu dữ liệu', 'Thiếu loai_doi_tuong_id!');
        }

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('trang_thai', true)
            ->orderBy('thu_tu', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $records = [];
        $masterMapping = [];

        $existingMasterMap = DB::table('doi_tuong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->pluck('id', 'ma_doi_tuong')
            ->toArray();

        if ($type->ma_loai === 'giang_vien') {
            $gvList = DB::table('giang_vien')->get();
            foreach ($gvList as $gv) {
                $msgv = (!empty($gv->email) && str_contains($gv->email, '@'))
                    ? explode('@', $gv->email)[0]
                    : ('GV_' . $gv->id_giang_vien);

                if (isset($existingMasterMap[$msgv])) {
                    $masterDoiTuongId = $existingMasterMap[$msgv];
                } else {
                    $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $msgv, $gv->ho_ten);
                    $existingMasterMap[$msgv] = $masterDoiTuongId;
                }

                $masterMapping[$masterDoiTuongId] = [
                    'id' => $gv->id_giang_vien,
                    'ma_doi_tuong' => $msgv,
                    'ten_hien_thi' => $gv->ho_ten,
                    'email' => $gv->email,
                    'attributes' => []
                ];
            }
        } elseif ($type->ma_loai === 'sinh_vien') {
            $svList = DB::table('sinh_vien')->get();
            foreach ($svList as $sv) {
                if (isset($existingMasterMap[$sv->mssv])) {
                    $masterDoiTuongId = $existingMasterMap[$sv->mssv];
                } else {
                    $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $sv->mssv, $sv->ho_ten);
                    $existingMasterMap[$sv->mssv] = $masterDoiTuongId;
                }

                $masterMapping[$masterDoiTuongId] = [
                    'id' => $sv->id,
                    'ma_doi_tuong' => $sv->mssv,
                    'ten_hien_thi' => $sv->ho_ten,
                    'email' => $sv->email,
                    'attributes' => []
                ];
            }
        } else {
            $dtList = DB::table('doi_tuong')->where('loai_doi_tuong_id', $loaiDoiTuongId)->get();
            foreach ($dtList as $dt) {
                $masterMapping[$dt->id] = [
                    'id' => $dt->id,
                    'ma_doi_tuong' => $dt->ma_doi_tuong,
                    'ten_hien_thi' => $dt->ten_hien_thi,
                    'attributes' => []
                ];
            }
        }

        // Bulk fetch dynamic attribute values to avoid N+1 query bug
        if (!empty($masterMapping) && $fields->isNotEmpty()) {
            $masterIds = array_keys($masterMapping);
            $fieldIds = $fields->pluck('id')->toArray();

            $valObjects = DB::table('gia_tri_thong_tin')
                ->whereIn('doi_tuong_id', $masterIds)
                ->whereIn('truong_id', $fieldIds)
                ->get();

            $valMap = [];
            foreach ($valObjects as $vo) {
                $valMap[$vo->doi_tuong_id . '_' . $vo->truong_id] = $vo->gia_tri;
            }

            foreach ($masterMapping as $mId => &$rec) {
                foreach ($fields as $field) {
                    $key = $mId . '_' . $field->id;
                    $rec['attributes'][$field->ma_truong] = $valMap[$key] ?? '';
                }
            }
        }

        $records = array_values($masterMapping);

        return Response::Success([
            'type' => $type,
            'fields' => $fields,
            'records' => $records
        ], 'Lấy danh sách bản ghi thành công');
    }

    public function putRecord(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.putRecord')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');
        $maDoiTuong = trim($request->input('ma_doi_tuong', ''));
        $tenHienThi = trim($request->input('ten_hien_thi', ''));
        $email = trim($request->input('email', ''));
        $attributes = $request->input('attributes', []);

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        if (empty($tenHienThi)) {
            return Response::Error('Sai định dạng dữ liệu', 'Vui lòng nhập tên hiển thị!');
        }

        $masterDoiTuongId = null;

        if ($type->ma_loai === 'giang_vien') {
            if (empty($email)) {
                return Response::Error('Sai định dạng dữ liệu', 'Giảng viên cần thông tin cơ bản là Email (Gmail) và Họ tên!');
            }
            if ($id) {
                DB::table('giang_vien')->where('id_giang_vien', $id)->update([
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('giang_vien')->insertGetId([
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'id_don_vi' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_giang_vien');
            }
            $msgv = str_contains($email, '@') ? explode('@', $email)[0] : ('GV_' . time());
            $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $msgv, $tenHienThi);
        } elseif ($type->ma_loai === 'sinh_vien') {
            if (empty($maDoiTuong) || empty($email)) {
                return Response::Error('Sai định dạng dữ liệu', 'Sinh viên cần MSSV, Email và Họ tên!');
            }
            if ($id) {
                DB::table('sinh_vien')->where('id', $id)->update([
                    'mssv' => $maDoiTuong,
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('sinh_vien')->insertGetId([
                    'mssv' => $maDoiTuong,
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $maDoiTuong, $tenHienThi);
        } else {
            if (empty($maDoiTuong)) {
                return Response::Error('Sai định dạng dữ liệu', 'Vui lòng nhập Mã đối tượng!');
            }
            $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $maDoiTuong, $tenHienThi);
        }

        $fields = DB::table('danh_muc_truong')->where('loai_doi_tuong_id', $loaiDoiTuongId)->get();
        foreach ($fields as $field) {
            if ($id && isset($field->cho_phep_chinh_sua) && !$field->cho_phep_chinh_sua) {
                continue;
            }
            $val = null;
            if ($request->hasFile("attributes.{$field->ma_truong}")) {
                $uploadedFile = $request->file("attributes.{$field->ma_truong}");
                $val = $this->uploadFileToS3($uploadedFile);
            } elseif (isset($attributes[$field->ma_truong])) {
                $rawVal = $attributes[$field->ma_truong];
                if (is_array($rawVal)) {
                    $val = json_encode($rawVal, JSON_UNESCAPED_UNICODE);
                } else {
                    $val = (string)$rawVal;
                }
            }

            if ($val !== null) {
                DB::table('gia_tri_thong_tin')->updateOrInsert(
                    [
                        'doi_tuong_id' => $masterDoiTuongId,
                        'truong_id' => $field->id,
                    ],
                    [
                        'gia_tri' => $val,
                        'ngay_tao' => now(),
                    ]
                );
            }
        }

        return Response::Success([], 'Lưu thông tin thành công!');
    }

    public function deleteRecord(Request $request, $id)
    {
        if (!$this->checkUserPermission('DynamicObjectController.deleteRecord')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();

        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        if ($type->ma_loai === 'giang_vien') {
            DB::table('giang_vien')->where('id_giang_vien', $id)->delete();
        } elseif ($type->ma_loai === 'sinh_vien') {
            DB::table('sinh_vien')->where('id', $id)->delete();
        } else {
            DB::table('doi_tuong')->where('id', $id)->delete();
        }

        DB::table('gia_tri_thong_tin')->where('doi_tuong_id', $id)->delete();

        return Response::Success([], 'Xóa bản ghi thành công!');
    }

    private static $doiTuongColumns = null;

    private function getDoiTuongColumns()
    {
        if (self::$doiTuongColumns === null) {
            self::$doiTuongColumns = DB::getSchemaBuilder()->getColumnListing('doi_tuong');
        }
        return self::$doiTuongColumns;
    }

    private function getOrCreateMasterDoiTuongId($loaiDoiTuongId, $maDoiTuong, $tenHienThi)
    {
        $dtMaster = DB::table('doi_tuong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('ma_doi_tuong', $maDoiTuong)
            ->first();

        $cols = $this->getDoiTuongColumns();
        $updateData = [];
        if (in_array('ten_hien_thi', $cols)) {
            $updateData['ten_hien_thi'] = $tenHienThi;
        } elseif (in_array('ten_doi_tuong', $cols)) {
            $updateData['ten_doi_tuong'] = $tenHienThi;
        }

        if (in_array('ngay_cap_nhat', $cols)) {
            $updateData['ngay_cap_nhat'] = now();
        }

        if ($dtMaster) {
            $currentName = $dtMaster->ten_hien_thi ?? $dtMaster->ten_doi_tuong ?? '';
            if ($currentName !== $tenHienThi && !empty($updateData)) {
                DB::table('doi_tuong')->where('id', $dtMaster->id)->update($updateData);
            }
            return $dtMaster->id;
        } else {
            $insertData = array_merge([
                'loai_doi_tuong_id' => $loaiDoiTuongId,
                'ma_doi_tuong' => $maDoiTuong,
            ], $updateData);

            if (in_array('ngay_tao', $cols)) {
                $insertData['ngay_tao'] = now();
            }

            return DB::table('doi_tuong')->insertGetId($insertData);
        }
    }

    private function uploadFileToS3($uploadedFile)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return '';
        }

        // Security check: validate size (max 20MB) and allowed extensions
        $maxSizeBytes = 20 * 1024 * 1024;
        if ($uploadedFile->getSize() > $maxSizeBytes) {
            return '';
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        if (!in_array($ext, $allowedExts)) {
            return '';
        }

        $disk = env('FILESYSTEM_DISK', 's3');
        $targetDisk = in_array($disk, ['s3', 'minio']) ? $disk : 's3';

        $path = $uploadedFile->store('dynamic_uploads', $targetDisk);
        if ($path) {
            $s3Endpoint = env('AWS_PUBLIC_ENDPOINT', env('AWS_ENDPOINT', 'http://localhost:9000'));
            $bucket = env('AWS_BUCKET', 'daotao-vlute-edu-vn');
            
            return rtrim($s3Endpoint, '/') . '/' . $bucket . '/' . $path;
        }

        $path = $uploadedFile->store('public/uploads');
        return '/storage/' . str_replace('public/', '', $path);
    }

    public static function resolveAttributeOptions($attr)
    {
        if ($attr->lien_ket_loai_doi_tuong_id) {
            $targetType = DB::table('loai_doi_tuong')->where('id', $attr->lien_ket_loai_doi_tuong_id)->first();
            if ($targetType) {
                $cauHinh = [];
                if (!empty($attr->cau_hinh)) {
                    $cauHinh = is_string($attr->cau_hinh) ? json_decode($attr->cau_hinh, true) : $attr->cau_hinh;
                }
                $bindMode = $cauHinh['tieu_chuan_gia_tri'] ?? 'ma';

                if ($targetType->ma_loai === 'giang_vien') {
                    $valCol = ($bindMode === 'ten') ? 'ho_ten' : 'email';
                    return DB::table('giang_vien')
                        ->select(DB::raw("ho_ten as text, {$valCol} as value"))
                        ->get();
                } elseif ($targetType->ma_loai === 'sinh_vien') {
                    $valCol = ($bindMode === 'ten') ? 'ho_ten' : 'mssv';
                    return DB::table('sinh_vien')
                        ->select(DB::raw("CONCAT(ho_ten, ' (', mssv, ')') as text, {$valCol} as value"))
                        ->get();
                } else {
                    $valCol = ($bindMode === 'ten') ? 'ten_hien_thi' : 'ma_doi_tuong';
                    return DB::table('doi_tuong')
                        ->where('loai_doi_tuong_id', $targetType->id)
                        ->select(DB::raw("ten_hien_thi as text, {$valCol} as value"))
                        ->get();
                }
            }
        }
        return null;
    }

    public function exportImportTemplate(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.exportImportTemplate')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('trang_thai', true)
            ->orderBy('thu_tu', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $headers = [];
        if ($type->ma_loai === 'giang_vien') {
            $headers[] = ['key' => 'email', 'label' => 'Email (*)', 'required' => true];
            $headers[] = ['key' => 'ten_hien_thi', 'label' => 'Họ và tên (*)', 'required' => true];
        } elseif ($type->ma_loai === 'sinh_vien') {
            $headers[] = ['key' => 'ma_doi_tuong', 'label' => 'MSHV/MSSV (*)', 'required' => true];
            $headers[] = ['key' => 'ten_hien_thi', 'label' => 'Họ và tên (*)', 'required' => true];
            $headers[] = ['key' => 'email', 'label' => 'Email (*)', 'required' => true];
        } else {
            $headers[] = ['key' => 'ma_doi_tuong', 'label' => 'Mã đối tượng (*)', 'required' => true];
            $headers[] = ['key' => 'ten_hien_thi', 'label' => 'Tên hiển thị (*)', 'required' => true];
        }

        foreach ($fields as $field) {
            $reqText = $field->bat_buoc ? ' (*)' : '';
            $headers[] = [
                'key' => $field->ma_truong,
                'label' => $field->ten_truong . ' [' . $field->ma_truong . ']' . $reqText,
                'required' => (bool)$field->bat_buoc
            ];
        }

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

        $colIndex = 1;
        foreach ($headers as $h) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}1", $h['label']);
            $colIndex++;
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A1:{$lastColLetter}1";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'bold' => true,
                'color' => ['rgb' => '1E293B'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $colIndex = 1;
        if ($type->ma_loai === 'giang_vien') {
            $sheet->setCellValue('A2', 'nguyenvana@vlute.edu.vn');
            $sheet->setCellValue('B2', 'Nguyễn Văn A');
            $colIndex = 3;
        } elseif ($type->ma_loai === 'sinh_vien') {
            $sheet->setCellValue('A2', '21001001');
            $sheet->setCellValue('B2', 'Trần Thị B');
            $sheet->setCellValue('C2', '21001001@st.vlute.edu.vn');
            $colIndex = 4;
        } else {
            $sheet->setCellValue('A2', 'MA_001');
            $sheet->setCellValue('B2', 'Đối tượng mẫu A');
            $colIndex = 3;
        }
        foreach ($fields as $field) {
            $sampleVal = 'Dữ liệu mẫu';
            if ($field->kieu_du_lieu === 'date') $sampleVal = '2026-01-01';
            elseif ($field->kieu_du_lieu === 'number') $sampleVal = '100';
            elseif ($field->kieu_du_lieu === 'boolean') $sampleVal = 'Có';
            elseif (in_array($field->kieu_du_lieu, ['file', 'image', 'tep_tin', 'tep_hinh_anh', 'file_url', 'image_url'])) $sampleVal = 'https://example.com/sample_file.pdf';

            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}2", $sampleVal);
            $colIndex++;
        }
        $sheet->getRowDimension(2)->setRowHeight(22);

        $sheet->getStyle("A1:{$lastColLetter}2")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '475569'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

        for ($i = 1; $i <= count($headers); $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Template_Import_' . $type->ma_loai . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function putPreviewImport(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.putPreviewImport')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $file = $request->file('file');
        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');

        if (!$file || !$loaiDoiTuongId) {
            return Response::Error('Sai định dạng dữ liệu', 'Vui lòng cung cấp tệp và loai_doi_tuong_id!');
        }

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('trang_thai', true)
            ->get();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows) || count($rows) < 2) {
            return Response::Error('Sai dữ liệu', 'File không chứa dữ liệu hoặc thiếu hàng tiêu đề!');
        }

        $headerRow = array_shift($rows);
        $columnMap = [];

        foreach ($headerRow as $colLetter => $headerText) {
            if (empty($headerText)) continue;
            $cleanHeader = strtolower(trim($headerText));
            
            if (str_contains($cleanHeader, 'mã đối tượng') || str_contains($cleanHeader, 'mã số sinh viên') || str_contains($cleanHeader, 'mssv')) {
                $columnMap[$colLetter] = 'ma_doi_tuong';
            } elseif (str_contains($cleanHeader, 'tên hiển thị') || str_contains($cleanHeader, 'họ và tên') || str_contains($cleanHeader, 'ho ten')) {
                $columnMap[$colLetter] = 'ten_hien_thi';
            } elseif (str_contains($cleanHeader, 'email')) {
                $columnMap[$colLetter] = 'email';
            } else {
                foreach ($fields as $field) {
                    if (str_contains($cleanHeader, '[' . strtolower($field->ma_truong) . ']') || str_contains($cleanHeader, strtolower($field->ma_truong)) || str_contains($cleanHeader, strtolower($field->ten_truong))) {
                        $columnMap[$colLetter] = 'field_' . $field->ma_truong;
                        break;
                    }
                }
            }
        }

        $existingCodes = [];
        if ($type->ma_loai === 'giang_vien') {
            $existingCodes = DB::table('giang_vien')->pluck('email')->toArray();
        } elseif ($type->ma_loai === 'sinh_vien') {
            $existingCodes = DB::table('sinh_vien')->pluck('mssv')->toArray();
        } else {
            $existingCodes = DB::table('doi_tuong')->where('loai_doi_tuong_id', $loaiDoiTuongId)->pluck('ma_doi_tuong')->toArray();
        }

        $mode = $request->input('mode', 'upsert');

        $parsedRows = [];
        $newCount = 0;
        $updateCount = 0;
        $skipCount = 0;
        $errorCount = 0;
        $seenCodes = [];

        foreach ($rows as $rowIndex => $row) {
            $isEmptyRow = true;
            foreach ($row as $val) {
                if (!empty(trim((string)$val))) {
                    $isEmptyRow = false;
                    break;
                }
            }
            if ($isEmptyRow) continue;

            $rowData = [
                'row_index' => $rowIndex + 1,
                'ma_doi_tuong' => '',
                'ten_hien_thi' => '',
                'email' => '',
                'attributes' => [],
                'errors' => [],
                'exists_in_db' => false,
                'action_type' => 'new'
            ];

            foreach ($columnMap as $colLetter => $key) {
                $cellVal = trim((string)($row[$colLetter] ?? ''));
                if ($key === 'ma_doi_tuong') {
                    $rowData['ma_doi_tuong'] = $cellVal;
                } elseif ($key === 'ten_hien_thi') {
                    $rowData['ten_hien_thi'] = $cellVal;
                } elseif ($key === 'email') {
                    $rowData['email'] = $cellVal;
                } elseif (str_starts_with($key, 'field_')) {
                    $maTruong = substr($key, 6);
                    $rowData['attributes'][$maTruong] = $cellVal;
                }
            }

            $keyVal = ($type->ma_loai === 'giang_vien') ? $rowData['email'] : $rowData['ma_doi_tuong'];

            if (empty($keyVal)) {
                $rowData['errors'][] = ($type->ma_loai === 'giang_vien') ? 'Thiếu Email giảng viên' : 'Thiếu Mã đối tượng / MSSV';
            }
            if (empty($rowData['ten_hien_thi'])) {
                $rowData['errors'][] = 'Thiếu Tên hiển thị / Họ tên';
            }

            foreach ($fields as $field) {
                $val = trim((string)($rowData['attributes'][$field->ma_truong] ?? ''));

                if ($field->bat_buoc && $val === '') {
                    $rowData['errors'][] = 'Thiếu thuộc tính bắt buộc: ' . $field->ten_truong;
                    continue;
                }

                if ($val === '') continue;

                $kieu = $field->kieu_du_lieu ?? 'text';

                if ($kieu === 'number') {
                    $rawNum = $val;
                    if (substr_count($rawNum, ',') === 1 && !str_contains($rawNum, '.')) {
                        $rawNum = str_replace(',', '.', $rawNum);
                    }
                    $cleanNum = str_replace([' ', ' '], '', $rawNum);
                    if (!is_numeric($cleanNum)) {
                        $rowData['errors'][] = "Thuộc tính '{$field->ten_truong}' phải là số hợp lệ (giá trị nhập: '{$val}')";
                    } else {
                        $rowData['attributes'][$field->ma_truong] = (string)(0 + $cleanNum);
                    }
                } elseif (in_array($kieu, ['date', 'datetime'])) {
                    $timestamp = strtotime($val);
                    if (!$timestamp && is_numeric($val)) {
                        try {
                            $dtObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                            $rowData['attributes'][$field->ma_truong] = ($kieu === 'date') ? $dtObj->format('Y-m-d') : $dtObj->format('Y-m-d H:i:s');
                            $timestamp = true;
                        } catch (\Exception $e) {
                            $timestamp = false;
                        }
                    }
                    if (!$timestamp) {
                        $rowData['errors'][] = "Thuộc tính '{$field->ten_truong}' sai định dạng ngày tháng, giá trị: '{$val}'";
                    }
                } elseif ($kieu === 'email') {
                    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                        $rowData['errors'][] = "Thuộc tính '{$field->ten_truong}' sai định dạng Email, giá trị: '{$val}'";
                    }
                } elseif ($kieu === 'boolean') {
                    $lowerVal = strtolower($val);
                    if (in_array($lowerVal, ['1', 'true', 'có', 'co', 'yes', 'kích hoạt'])) {
                        $rowData['attributes'][$field->ma_truong] = '1';
                    } elseif (in_array($lowerVal, ['0', 'false', 'không', 'khong', 'no', 'tắt'])) {
                        $rowData['attributes'][$field->ma_truong] = '0';
                    } else {
                        $rowData['errors'][] = "Thuộc tính '{$field->ten_truong}' kiểu Đúng/Sai chỉ nhận: Có/Không, True/False, 1/0";
                    }
                } elseif (in_array($kieu, ['file', 'image', 'tep_tin', 'tep_hinh_anh', 'file_url', 'image_url'])) {
                    $isLink = (
                        str_starts_with($val, 'http://') || 
                        str_starts_with($val, 'https://') || 
                        str_starts_with($val, 's3://') || 
                        str_starts_with($val, '/') ||
                        filter_var($val, FILTER_VALIDATE_URL)
                    );
                    if (!$isLink) {
                        if ($field->bat_buoc) {
                            $rowData['errors'][] = "Thuộc tính '{$field->ten_truong}' bắt buộc phải là đường dẫn tệp (URL hợp lệ, VD: https://...)";
                        } else {
                            $rowData['attributes'][$field->ma_truong] = '';
                        }
                    }
                }
            }

            if (!empty($keyVal)) {
                if (in_array($keyVal, $seenCodes)) {
                    $rowData['errors'][] = 'Trùng lặp mã "' . $keyVal . '" trong tệp import';
                } else {
                    $seenCodes[] = $keyVal;
                }
            }

            if (!empty($rowData['errors'])) {
                $rowData['action_type'] = 'error';
                $errorCount++;
            } else {
                $rowData['exists_in_db'] = in_array($keyVal, $existingCodes);
                if ($mode === 'insert_new') {
                    if ($rowData['exists_in_db']) {
                        $rowData['action_type'] = 'skip';
                        $rowData['errors'][] = 'Đã bỏ qua: Mã đối tượng đã tồn tại trong DB (Chế độ: Chỉ thêm mới)';
                        $skipCount++;
                    } else {
                        $rowData['action_type'] = 'new';
                        $newCount++;
                    }
                } elseif ($mode === 'update_existing') {
                    if ($rowData['exists_in_db']) {
                        $rowData['action_type'] = 'update';
                        $updateCount++;
                    } else {
                        $rowData['action_type'] = 'skip';
                        $rowData['errors'][] = 'Đã bỏ qua: Mã đối tượng chưa có trong DB (Chế độ: Chỉ cập nhật)';
                        $skipCount++;
                    }
                } else {
                    if ($rowData['exists_in_db']) {
                        $rowData['action_type'] = 'update';
                        $updateCount++;
                    } else {
                        $rowData['action_type'] = 'new';
                        $newCount++;
                    }
                }
            }

            $parsedRows[] = $rowData;
        }

        return Response::Success([
            'total_rows' => count($parsedRows),
            'new_count' => $newCount,
            'update_count' => $updateCount,
            'skip_count' => $skipCount,
            'error_count' => $errorCount,
            'fields' => $fields,
            'rows' => $parsedRows
        ], 'Xem trước dữ liệu import thành công');
    }

    public function putProcessImport(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.putProcessImport')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');
        $mode = $request->input('mode', 'upsert');
        $rows = $request->input('rows', []);

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        if (empty($rows)) {
            return Response::Error('Thiếu dữ liệu', 'Không có hàng dữ liệu nào để import!');
        }

        $fields = DB::table('danh_muc_truong')->where('loai_doi_tuong_id', $loaiDoiTuongId)->get();
        $autoClearErrors = (bool)($request->input('auto_clear_errors', false));

        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($rows, $type, $loaiDoiTuongId, $mode, $fields, $autoClearErrors, &$inserted, &$updated, &$skipped) {
            foreach ($rows as $row) {
                $maDoiTuong = trim($row['ma_doi_tuong'] ?? '');
                $tenHienThi = trim($row['ten_hien_thi'] ?? '');
                $email = trim($row['email'] ?? '');
                $attributes = $row['attributes'] ?? [];

                $keyVal = ($type->ma_loai === 'giang_vien') ? $email : $maDoiTuong;
                if (empty($keyVal) || empty($tenHienThi)) {
                    $skipped++;
                    continue;
                }

                if (!empty($row['errors']) || $row['action_type'] === 'error') {
                    if (!$autoClearErrors) {
                        $skipped++;
                        continue;
                    }
                }

                $exists = (bool)($row['exists_in_db'] ?? false);

                if ($mode === 'insert_new' && $exists) {
                    $skipped++;
                    continue;
                }
                if ($mode === 'update_existing' && !$exists) {
                    $skipped++;
                    continue;
                }

                $masterDoiTuongId = null;

                if ($type->ma_loai === 'giang_vien') {
                    $existingGv = DB::table('giang_vien')->where('email', $email)->first();
                    if ($existingGv) {
                        DB::table('giang_vien')->where('id_giang_vien', $existingGv->id_giang_vien)->update([
                            'ho_ten' => $tenHienThi,
                            'updated_at' => now(),
                        ]);
                        $updated++;
                    } else {
                        DB::table('giang_vien')->insertGetId([
                            'ho_ten' => $tenHienThi,
                            'email' => $email,
                            'id_don_vi' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ], 'id_giang_vien');
                        $inserted++;
                    }
                    $msgv = str_contains($email, '@') ? explode('@', $email)[0] : ('GV_' . time());
                    $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $msgv, $tenHienThi);
                } elseif ($type->ma_loai === 'sinh_vien') {
                    $existingSv = DB::table('sinh_vien')->where('mssv', $maDoiTuong)->first();
                    if ($existingSv) {
                        DB::table('sinh_vien')->where('id', $existingSv->id)->update([
                            'ho_ten' => $tenHienThi,
                            'email' => empty($email) ? $existingSv->email : $email,
                            'updated_at' => now(),
                        ]);
                        $updated++;
                    } else {
                        DB::table('sinh_vien')->insertGetId([
                            'mssv' => $maDoiTuong,
                            'ho_ten' => $tenHienThi,
                            'email' => empty($email) ? ($maDoiTuong . '@st.vlute.edu.vn') : $email,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $inserted++;
                    }
                    $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $maDoiTuong, $tenHienThi);
                } else {
                    $existingDt = DB::table('doi_tuong')
                        ->where('loai_doi_tuong_id', $loaiDoiTuongId)
                        ->where('ma_doi_tuong', $maDoiTuong)
                        ->first();
                    if ($existingDt) {
                        $updated++;
                    } else {
                        $inserted++;
                    }
                    $masterDoiTuongId = $this->getOrCreateMasterDoiTuongId($loaiDoiTuongId, $maDoiTuong, $tenHienThi);
                }

                foreach ($fields as $field) {
                    if (isset($attributes[$field->ma_truong])) {
                        $val = (string)$attributes[$field->ma_truong];
                        DB::table('gia_tri_thong_tin')->updateOrInsert(
                            [
                                'doi_tuong_id' => $masterDoiTuongId,
                                'truong_id' => $field->id,
                            ],
                            [
                                'gia_tri' => $val,
                                'ngay_tao' => now(),
                            ]
                        );
                    }
                }
            }
        });

        return Response::Success([
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped
        ], "Import dữ liệu hoàn tất! Thêm mới: {$inserted}, Cập nhật: {$updated}, Bỏ qua: {$skipped}.");
    }

    public function exportRecords(Request $request)
    {
        if (!$this->checkUserPermission('DynamicObjectController.exportRecords')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy loại đối tượng!');
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('trang_thai', true)
            ->orderBy('thu_tu', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $recordsRes = $this->getRecords($request);
        $recordsData = json_decode($recordsRes->getContent(), true)['data'] ?? [];
        $records = $recordsData['records'] ?? [];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dữ liệu ' . mb_substr($type->ten_loai, 0, 20));

        $headers = [];
        if ($type->ma_loai === 'giang_vien') {
            $headers[] = ['key' => 'email', 'label' => 'Email'];
            $headers[] = ['key' => 'ten_hien_thi', 'label' => 'Họ và tên'];
        } elseif ($type->ma_loai === 'sinh_vien') {
            $headers[] = ['key' => 'ma_doi_tuong', 'label' => 'MSHV/MSSV'];
            $headers[] = ['key' => 'ten_hien_thi', 'label' => 'Họ và tên'];
            $headers[] = ['key' => 'email', 'label' => 'Email'];
        } else {
            $headers[] = ['key' => 'ma_doi_tuong', 'label' => 'Mã đối tượng'];
            $headers[] = ['key' => 'ten_hien_thi', 'label' => 'Tên hiển thị'];
        }

        foreach ($fields as $field) {
            $headers[] = [
                'key' => $field->ma_truong,
                'label' => $field->ten_truong
            ];
        }

        $colIndex = 1;
        foreach ($headers as $h) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}1", $h['label']);
            $colIndex++;
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastColLetter}1")->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'bold' => true,
                'color' => ['rgb' => '1E293B'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowIndex = 2;
        foreach ($records as $rec) {
            $colIndex = 1;
            if ($type->ma_loai === 'giang_vien') {
                $sheet->setCellValue("A{$rowIndex}", $rec['email'] ?? '');
                $sheet->setCellValue("B{$rowIndex}", $rec['ten_hien_thi'] ?? '');
                $colIndex = 3;
            } elseif ($type->ma_loai === 'sinh_vien') {
                $sheet->setCellValue("A{$rowIndex}", $rec['ma_doi_tuong'] ?? '');
                $sheet->setCellValue("B{$rowIndex}", $rec['ten_hien_thi'] ?? '');
                $sheet->setCellValue("C{$rowIndex}", $rec['email'] ?? '');
                $colIndex = 4;
            } else {
                $sheet->setCellValue("A{$rowIndex}", $rec['ma_doi_tuong'] ?? '');
                $sheet->setCellValue("B{$rowIndex}", $rec['ten_hien_thi'] ?? '');
                $colIndex = 3;
            }

            foreach ($fields as $field) {
                $rawVal = $rec['attributes'][$field->ma_truong] ?? '';
                $valStr = $rawVal;

                if ($field->kieu_du_lieu === 'boolean') {
                    $valStr = ($rawVal == '1' || $rawVal === 'true' || $rawVal === 'Có') ? 'Có' : 'Không';
                } elseif (is_array($rawVal)) {
                    $valStr = implode(', ', $rawVal);
                }

                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$colLetter}{$rowIndex}", $valStr);
                $colIndex++;
            }
            $sheet->getRowDimension($rowIndex)->setRowHeight(22);
            $rowIndex++;
        }

        $lastRow = max(2, $rowIndex - 1);
        $sheet->getStyle("A1:{$lastColLetter}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '475569'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ]);

        for ($i = 1; $i <= count($headers); $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Danh_Sach_' . $type->ma_loai . '_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function getLayoutConfig(Request $request)
    {
        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        if (!$loaiDoiTuongId) {
            return Response::Error('Thiếu dữ liệu', 'Vui lòng cung cấp loai_doi_tuong_id');
        }

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return Response::Error('Không tìm thấy', 'Loại đối tượng không tồn tại');
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('trang_thai', true)
            ->orderBy('thu_tu', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($field) {
                $cauHinh = [];
                if ($field->cau_hinh) {
                    try {
                        $cauHinh = is_string($field->cau_hinh) ? json_decode($field->cau_hinh, true) : (array)$field->cau_hinh;
                    } catch (\Exception $e) {}
                }
                
                $defaultColSpan = in_array($field->kieu_du_lieu, ['textarea', 'file', 'image']) ? 12 : 6;
                $field->col_span = intval($cauHinh['col_span'] ?? $defaultColSpan);
                $field->cau_hinh = $cauHinh;
                return $field;
            });

        return Response::Success([
            'type' => $type,
            'fields' => $fields
        ], 'Lấy cấu hình bố cục thành công');
    }

    public function putLayoutConfig(Request $request)
    {
        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');
        $layoutItems = $request->input('layout_items', []);

        if (!$loaiDoiTuongId) {
            return Response::Error('Thiếu dữ liệu', 'Vui lòng cung cấp loai_doi_tuong_id');
        }

        if (is_string($layoutItems)) {
            $layoutItems = json_decode($layoutItems, true) ?? [];
        }

        DB::transaction(function () use ($layoutItems) {
            foreach ($layoutItems as $index => $item) {
                $id = $item['id'] ?? null;
                if (!$id) continue;

                $field = DB::table('danh_muc_truong')->where('id', $id)->first();
                if (!$field) continue;

                $cauHinh = [];
                if ($field->cau_hinh) {
                    try {
                        $cauHinh = is_string($field->cau_hinh) ? json_decode($field->cau_hinh, true) : (array)$field->cau_hinh;
                    } catch (\Exception $e) {}
                }

                if (isset($item['col_span'])) {
                    $cauHinh['col_span'] = intval($item['col_span']);
                }

                $updateData = [
                    'thu_tu' => intval($item['thu_tu'] ?? ($index + 1)),
                    'cau_hinh' => json_encode($cauHinh, JSON_UNESCAPED_UNICODE)
                ];

                if (isset($item['phan_nhom']) && !empty($item['phan_nhom'])) {
                    $updateData['phan_nhom'] = trim($item['phan_nhom']);
                }

                DB::table('danh_muc_truong')->where('id', $id)->update($updateData);
            }
        });

        return Response::Success(null, 'Cập nhật bố cục trang thành công');
    }
}
