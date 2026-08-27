<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicObjectController extends Controller
{
    /**
     * Get list of all entity types (Loại đối tượng)
     */
    public function getTypes()
    {
        // Seed default types if missing
        $this->ensureDefaultTypesSeeded();

        $types = DB::table('loai_doi_tuong')
            ->select('loai_doi_tuong.*')
            ->get();

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

        return response()->json([
            'status' => 200,
            'data' => $types
        ]);
    }

    /**
     * Create or update entity type
     */
    public function saveType(Request $request)
    {
        $id = $request->input('id');
        $maLoai = trim($request->input('ma_loai'));
        $tenLoai = trim($request->input('ten_loai'));
        $moTa = trim($request->input('mo_ta', ''));

        if (empty($maLoai) || empty($tenLoai)) {
            return response()->json(['status' => 400, 'message' => 'Vui lòng nhập Mã loại và Tên loại đối tượng!'], 400);
        }

        if ($id) {
            DB::table('loai_doi_tuong')->where('id', $id)->update([
                'ma_loai' => $maLoai,
                'ten_loai' => $tenLoai,
                'mo_ta' => $moTa,
            ]);
        } else {
            // Check unique code
            $exists = DB::table('loai_doi_tuong')->where('ma_loai', $maLoai)->exists();
            if ($exists) {
                return response()->json(['status' => 400, 'message' => 'Mã loại đối tượng này đã tồn tại!'], 400);
            }

            DB::table('loai_doi_tuong')->insert([
                'ma_loai' => $maLoai,
                'ten_loai' => $tenLoai,
                'mo_ta' => $moTa,
                'ngay_tao' => now(),
            ]);
        }

        return response()->json(['status' => 200, 'message' => 'Lưu thông tin loại đối tượng thành công!']);
    }

    /**
     * Delete entity type
     */
    public function deleteType($id)
    {
        $type = DB::table('loai_doi_tuong')->where('id', $id)->first();
        if (!$type) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy loại đối tượng!'], 404);
        }

        if (in_array($type->ma_loai, ['giang_vien', 'sinh_vien'])) {
            return response()->json(['status' => 400, 'message' => 'Không thể xóa loại đối tượng mặc định của hệ thống!'], 400);
        }

        DB::table('loai_doi_tuong')->where('id', $id)->delete();

        return response()->json(['status' => 200, 'message' => 'Xóa loại đối tượng thành công!']);
    }

    /**
     * Get fields (Thuộc tính) for a specific entity type
     */
    public function getFields(Request $request)
    {
        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        if (!$loaiDoiTuongId) {
            return response()->json(['status' => 400, 'message' => 'Thiếu loai_doi_tuong_id!'], 400);
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

        return response()->json([
            'status' => 200,
            'data' => $fields
        ]);
    }

    /**
     * Create or update field (Thuộc tính động)
     */
    public function saveField(Request $request)
    {
        $id = $request->input('id');
        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');
        $maTruong = trim($request->input('ma_truong'));
        $tenTruong = trim($request->input('ten_truong'));
        $phanNhom = trim($request->input('phan_nhom', 'Thông tin bổ sung'));
        $kieuDuLieu = $request->input('kieu_du_lieu', 'text');
        $trangThai = $request->input('trang_thai', true);
        $thuTu = intval($request->input('thu_tu', 0));

        if (!$loaiDoiTuongId || empty($maTruong) || empty($tenTruong)) {
            return response()->json(['status' => 400, 'message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc!'], 400);
        }

        $lienKetLoaiDoiTuongId = $request->input('lien_ket_loai_doi_tuong_id');
        $luaChon = $request->input('lua_chon');
        $batBuoc = $request->input('bat_buoc', false);
        $cauHinh = $request->input('cau_hinh');

        $data = [
            'ma_truong' => $maTruong,
            'ten_truong' => $tenTruong,
            'phan_nhom' => empty($phanNhom) ? 'Thông tin bổ sung' : $phanNhom,
            'kieu_du_lieu' => $kieuDuLieu,
            'trang_thai' => $trangThai,
            'thu_tu' => $thuTu,
            'bat_buoc' => (bool)$batBuoc,
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
                return response()->json(['status' => 400, 'message' => 'Mã thuộc tính đã tồn tại trong loại đối tượng này!'], 400);
            }

            $data['loai_doi_tuong_id'] = $loaiDoiTuongId;
            $data['ngay_tao'] = now();
            DB::table('danh_muc_truong')->insert($data);
        }

        return response()->json(['status' => 200, 'message' => 'Lưu thuộc tính thành công!']);
    }

    /**
     * Batch reorder fields and update group assignment (Sắp xếp thứ tự & chuyển nhóm thuộc tính)
     */
    public function reorderFields(Request $request)
    {
        $orders = $request->input('orders', []);
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
        return response()->json(['status' => 200, 'message' => 'Cập nhật thứ tự sắp xếp và phân nhóm thành công!']);
    }

    /**
     * Delete field
     */
    public function deleteField($id)
    {
        DB::table('danh_muc_truong')->where('id', $id)->delete();
        return response()->json(['status' => 200, 'message' => 'Xóa thuộc tính thành công!']);
    }

    /**
     * Get object records & dynamic values for an entity type
     */
    public function getRecords(Request $request)
    {
        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        if (!$loaiDoiTuongId) {
            return response()->json(['status' => 400, 'message' => 'Thiếu loai_doi_tuong_id!'], 400);
        }

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy loại đối tượng!'], 404);
        }

        $fields = DB::table('danh_muc_truong')
            ->where('loai_doi_tuong_id', $loaiDoiTuongId)
            ->where('trang_thai', true)
            ->orderBy('thu_tu', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $records = [];

        if ($type->ma_loai === 'giang_vien') {
            $gvList = DB::table('giang_vien')->get();
            foreach ($gvList as $gv) {
                $msgv = (!empty($gv->email) && str_contains($gv->email, '@'))
                    ? explode('@', $gv->email)[0]
                    : ('GV_' . $gv->id_giang_vien);
                $rec = [
                    'id' => $gv->id_giang_vien,
                    'ma_doi_tuong' => $msgv,
                    'ten_hien_thi' => $gv->ho_ten,
                    'email' => $gv->email,
                    'attributes' => []
                ];
                // Fetch extended dynamic values
                foreach ($fields as $field) {
                    $valObj = DB::table('gia_tri_thong_tin')
                        ->where('doi_tuong_id', $gv->id_giang_vien)
                        ->where('truong_id', $field->id)
                        ->first();
                    $rec['attributes'][$field->ma_truong] = $valObj ? $valObj->gia_tri : '';
                }
                $records[] = $rec;
            }
        } elseif ($type->ma_loai === 'sinh_vien') {
            $svList = DB::table('sinh_vien')->get();
            foreach ($svList as $sv) {
                $rec = [
                    'id' => $sv->id,
                    'ma_doi_tuong' => $sv->mssv,
                    'ten_hien_thi' => $sv->ho_ten,
                    'email' => $sv->email,
                    'attributes' => []
                ];
                foreach ($fields as $field) {
                    $valObj = DB::table('gia_tri_thong_tin')
                        ->where('doi_tuong_id', $sv->id)
                        ->where('truong_id', $field->id)
                        ->first();
                    $rec['attributes'][$field->ma_truong] = $valObj ? $valObj->gia_tri : '';
                }
                $records[] = $rec;
            }
        } else {
            $dtList = DB::table('doi_tuong')->where('loai_doi_tuong_id', $loaiDoiTuongId)->get();
            foreach ($dtList as $dt) {
                $rec = [
                    'id' => $dt->id,
                    'ma_doi_tuong' => $dt->ma_doi_tuong,
                    'ten_hien_thi' => $dt->ten_hien_thi,
                    'attributes' => []
                ];
                foreach ($fields as $field) {
                    $valObj = DB::table('gia_tri_thong_tin')
                        ->where('doi_tuong_id', $dt->id)
                        ->where('truong_id', $field->id)
                        ->first();
                    $rec['attributes'][$field->ma_truong] = $valObj ? $valObj->gia_tri : '';
                }
                $records[] = $rec;
            }
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'type' => $type,
                'fields' => $fields,
                'records' => $records
            ]
        ]);
    }

    /**
     * Create/Update Record and its Dynamic Attribute Values
     */
    public function saveRecord(Request $request)
    {
        $id = $request->input('id');
        $loaiDoiTuongId = $request->input('loai_doi_tuong_id');
        $maDoiTuong = trim($request->input('ma_doi_tuong'));
        $tenHienThi = trim($request->input('ten_hien_thi'));
        $email = trim($request->input('email', ''));
        $attributes = $request->input('attributes', []);

        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();
        if (!$type) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy loại đối tượng!'], 404);
        }

        if (empty($tenHienThi)) {
            return response()->json(['status' => 400, 'message' => 'Vui lòng nhập tên hiển thị!'], 400);
        }

        $recordId = null;

        if ($type->ma_loai === 'giang_vien') {
            if (empty($email)) {
                return response()->json(['status' => 400, 'message' => 'Giảng viên cần thông tin cơ bản là Email (Gmail) và Họ tên!'], 400);
            }
            if ($id) {
                DB::table('giang_vien')->where('id_giang_vien', $id)->update([
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'updated_at' => now(),
                ]);
                $recordId = $id;
            } else {
                $recordId = DB::table('giang_vien')->insertGetId([
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'id_don_vi' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($type->ma_loai === 'sinh_vien') {
            if (empty($maDoiTuong) || empty($email)) {
                return response()->json(['status' => 400, 'message' => 'Sinh viên cần MSSV, Email và Họ tên!'], 400);
            }
            if ($id) {
                DB::table('sinh_vien')->where('id', $id)->update([
                    'mssv' => $maDoiTuong,
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'updated_at' => now(),
                ]);
                $recordId = $id;
            } else {
                $recordId = DB::table('sinh_vien')->insertGetId([
                    'mssv' => $maDoiTuong,
                    'ho_ten' => $tenHienThi,
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            if (empty($maDoiTuong)) {
                return response()->json(['status' => 400, 'message' => 'Vui lòng nhập Mã đối tượng!'], 400);
            }
            if ($id) {
                DB::table('doi_tuong')->where('id', $id)->update([
                    'ma_doi_tuong' => $maDoiTuong,
                    'ten_hien_thi' => $tenHienThi,
                ]);
                $recordId = $id;
            } else {
                $recordId = DB::table('doi_tuong')->insertGetId([
                    'loai_doi_tuong_id' => $loaiDoiTuongId,
                    'ma_doi_tuong' => $maDoiTuong,
                    'ten_hien_thi' => $tenHienThi,
                    'ngay_tao' => now(),
                ]);
            }
        }

        // Save dynamic attribute values (handling text, multiselect, S3 file uploads, etc.)
        $fields = DB::table('danh_muc_truong')->where('loai_doi_tuong_id', $loaiDoiTuongId)->get();
        foreach ($fields as $field) {
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
                        'doi_tuong_id' => $recordId,
                        'truong_id' => $field->id,
                    ],
                    [
                        'gia_tri' => $val,
                        'ngay_tao' => now(),
                    ]
                );
            }
        }

        return response()->json(['status' => 200, 'message' => 'Lưu thông tin thành công!']);
    }

    /**
     * Delete Record
     */
    public function deleteRecord(Request $request, $id)
    {
        $loaiDoiTuongId = $request->query('loai_doi_tuong_id');
        $type = DB::table('loai_doi_tuong')->where('id', $loaiDoiTuongId)->first();

        if (!$type) {
            return response()->json(['status' => 404, 'message' => 'Không tìm thấy loại đối tượng!'], 404);
        }

        if ($type->ma_loai === 'giang_vien') {
            DB::table('giang_vien')->where('id_giang_vien', $id)->delete();
        } elseif ($type->ma_loai === 'sinh_vien') {
            DB::table('sinh_vien')->where('id', $id)->delete();
        } else {
            DB::table('doi_tuong')->where('id', $id)->delete();
        }

        DB::table('gia_tri_thong_tin')->where('doi_tuong_id', $id)->delete();

        return response()->json(['status' => 200, 'message' => 'Xóa bản ghi thành công!']);
    }

    /**
     * Internal helper to ensure standard entity types exist
     */
    private function ensureDefaultTypesSeeded()
    {
        // Add phan_nhom column to danh_muc_truong if not exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('danh_muc_truong', 'phan_nhom')) {
            \Illuminate\Support\Facades\Schema::table('danh_muc_truong', function ($table) {
                $table->string('phan_nhom', 100)->nullable()->default('Thông tin bổ sung');
            });
        }

        // Add thu_tu column to danh_muc_truong if not exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('danh_muc_truong', 'thu_tu')) {
            \Illuminate\Support\Facades\Schema::table('danh_muc_truong', function ($table) {
                $table->integer('thu_tu')->default(0);
            });
        }

        // Add lien_ket_loai_doi_tuong_id column to danh_muc_truong if not exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('danh_muc_truong', 'lien_ket_loai_doi_tuong_id')) {
            \Illuminate\Support\Facades\Schema::table('danh_muc_truong', function ($table) {
                $table->unsignedBigInteger('lien_ket_loai_doi_tuong_id')->nullable();
            });
        }

        // Add lua_chon column to danh_muc_truong if not exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('danh_muc_truong', 'lua_chon')) {
            \Illuminate\Support\Facades\Schema::table('danh_muc_truong', function ($table) {
                $table->text('lua_chon')->nullable();
            });
        }

        // Add bat_buoc column to danh_muc_truong if not exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('danh_muc_truong', 'bat_buoc')) {
            \Illuminate\Support\Facades\Schema::table('danh_muc_truong', function ($table) {
                $table->boolean('bat_buoc')->default(false);
            });
        }

        // Add cau_hinh column to danh_muc_truong if not exists
        if (!\Illuminate\Support\Facades\Schema::hasColumn('danh_muc_truong', 'cau_hinh')) {
            \Illuminate\Support\Facades\Schema::table('danh_muc_truong', function ($table) {
                $table->text('cau_hinh')->nullable();
            });
        }

        $defaults = [
            [
                'ma_loai' => 'giang_vien',
                'ten_loai' => 'Giảng viên',
                'mo_ta' => 'Quản lý thông tin giảng viên (Email Gmail & Họ tên cơ bản + các thuộc tính bổ sung)'
            ],
            [
                'ma_loai' => 'sinh_vien',
                'ten_loai' => 'Sinh viên',
                'mo_ta' => 'Quản lý thông tin sinh viên cơ bản & thuộc tính học tập mở rộng'
            ],
            [
                'ma_loai' => 'phong_hoc',
                'ten_loai' => 'Phòng học & Hội trường',
                'mo_ta' => 'Quản lý phòng học, giảng đường, sức chứa và thiết bị giảng dạy'
            ],
            [
                'ma_loai' => 'thiet_bi',
                'ten_loai' => 'Thiết bị & Tài sản',
                'mo_ta' => 'Quản lý máy chiếu, máy tính, thiết bị thí nghiệm...'
            ]
        ];

        foreach ($defaults as $item) {
            $type = DB::table('loai_doi_tuong')->where('ma_loai', $item['ma_loai'])->first();
            if (!$type) {
                $typeId = DB::table('loai_doi_tuong')->insertGetId([
                    'ma_loai' => $item['ma_loai'],
                    'ten_loai' => $item['ten_loai'],
                    'mo_ta' => $item['mo_ta'],
                    'ngay_tao' => now(),
                ]);
            } else {
                $typeId = $type->id;
            }

            // Seed default grouped dynamic attributes for Giang vien if none exists
            if ($item['ma_loai'] === 'giang_vien') {
                $count = DB::table('danh_muc_truong')->where('loai_doi_tuong_id', $typeId)->count();
                if ($count === 0) {
                    DB::table('danh_muc_truong')->insert([
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'hoc_vi',
                            'ten_truong' => 'Học vị / Học hàm',
                            'phan_nhom' => 'Lý lịch & Chức danh',
                            'kieu_du_lieu' => 'select',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'chuc_danh',
                            'ten_truong' => 'Chức danh công tác',
                            'phan_nhom' => 'Lý lịch & Chức danh',
                            'kieu_du_lieu' => 'text',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'so_dien_thoai',
                            'ten_truong' => 'Số điện thoại liên hệ',
                            'phan_nhom' => 'Thông tin Liên hệ',
                            'kieu_du_lieu' => 'text',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'hinh_anh_the',
                            'ten_truong' => 'Hình ảnh chân dung',
                            'phan_nhom' => 'Hồ sơ & Minh chứng',
                            'kieu_du_lieu' => 'image',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'ly_lich_khoa_hoc',
                            'ten_truong' => 'Tệp CV / Lý lịch khoa học',
                            'phan_nhom' => 'Hồ sơ & Minh chứng',
                            'kieu_du_lieu' => 'file',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ]
                    ]);
                }
            }

            // Seed default grouped dynamic attributes for Sinh vien if none exists
            if ($item['ma_loai'] === 'sinh_vien') {
                $count = DB::table('danh_muc_truong')->where('loai_doi_tuong_id', $typeId)->count();
                if ($count === 0) {
                    DB::table('danh_muc_truong')->insert([
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'ngay_sinh',
                            'ten_truong' => 'Ngày sinh',
                            'phan_nhom' => 'Thông tin Lý lịch & Cá nhân',
                            'kieu_du_lieu' => 'date',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'gioi_tinh',
                            'ten_truong' => 'Giới tính',
                            'phan_nhom' => 'Thông tin Lý lịch & Cá nhân',
                            'kieu_du_lieu' => 'select',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'so_dien_thoai_sv',
                            'ten_truong' => 'Số điện thoại',
                            'phan_nhom' => 'Thông tin Lý lịch & Cá nhân',
                            'kieu_du_lieu' => 'text',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'nganh_hoc',
                            'ten_truong' => 'Ngành đào tạo',
                            'phan_nhom' => 'Thông tin Đào tạo & Lớp học',
                            'kieu_du_lieu' => 'text',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'lop_sinh_hoat',
                            'ten_truong' => 'Lớp sinh hoạt',
                            'phan_nhom' => 'Thông tin Đào tạo & Lớp học',
                            'kieu_du_lieu' => 'text',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'anh_the_3x4',
                            'ten_truong' => 'Ảnh thẻ 3x4',
                            'phan_nhom' => 'Hồ sơ đính kèm',
                            'kieu_du_lieu' => 'image',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ],
                        [
                            'loai_doi_tuong_id' => $typeId,
                            'ma_truong' => 'tep_minh_chung',
                            'ten_truong' => 'Tệp Bằng cấp / Minh chứng',
                            'phan_nhom' => 'Hồ sơ đính kèm',
                            'kieu_du_lieu' => 'file',
                            'trang_thai' => true,
                            'ngay_tao' => now(),
                        ]
                    ]);
                }
            }
        }
    }

    /**
     * Upload file to S3 / MinIO storage
     */
    private function uploadFileToS3($uploadedFile)
    {
        try {
            // Attempt upload to S3 / MinIO disk
            $disk = env('FILESYSTEM_DISK', 's3');
            $targetDisk = in_array($disk, ['s3', 'minio']) ? $disk : 's3';

            $path = $uploadedFile->store('dynamic_uploads', $targetDisk);
            if ($path) {
                // Build public S3 / MinIO URL based on env configuration
                $s3Endpoint = env('AWS_PUBLIC_ENDPOINT', env('AWS_ENDPOINT', 'http://localhost:9000'));
                $bucket = env('AWS_BUCKET', 'daotao-vlute-edu-vn');
                
                return rtrim($s3Endpoint, '/') . '/' . $bucket . '/' . $path;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("S3 Storage Upload Error: " . $e->getMessage());
        }

        // Fallback to public storage if S3 fails or is not reachable
        $path = $uploadedFile->store('public/uploads');
        return '/storage/' . str_replace('public/', '', $path);
    }

    /**
     * Helper to resolve dynamic option choices for select fields (from target entity type or custom list)
     */
    public static function resolveAttributeOptions($attr)
    {
        if ($attr->lien_ket_loai_doi_tuong_id) {
            $targetType = DB::table('loai_doi_tuong')->where('id', $attr->lien_ket_loai_doi_tuong_id)->first();
            if ($targetType) {
                // Check value binding rule (ma vs ten)
                $cauHinh = [];
                if (!empty($attr->cau_hinh)) {
                    $cauHinh = is_string($attr->cau_hinh) ? json_decode($attr->cau_hinh, true) : $attr->cau_hinh;
                }
                $bindMode = $cauHinh['tieu_chuan_gia_tri'] ?? 'ma'; // ma | ten

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
}
