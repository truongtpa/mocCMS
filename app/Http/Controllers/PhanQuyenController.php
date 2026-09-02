<?php

namespace App\Http\Controllers;

use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanQuyenController extends Controller
{
    private function checkUserPermission($permissionKey)
    {
        return \App\VLUTE::checkPermission($permissionKey);
    }

    public function getDanhSachVaiTro()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachVaiTro')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $dsVaiTro = DB::table('vai_tro')
            ->select('id', 'ma_vai_tro', 'ten_vai_tro', 'created_at', 'updated_at')
            ->orderBy('id', 'asc')
            ->get();

        return Response::Success($dsVaiTro, 'Lấy danh sách vai trò thành công');
    }

    public function putVaiTro(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.putVaiTro')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $maVaiTro = trim($request->input('ma_vai_tro', ''));
        $tenVaiTro = trim($request->input('ten_vai_tro', ''));

        $errors = [];
        if (empty($maVaiTro)) $errors[] = 'Mã vai trò không được bỏ trống';
        if (empty($tenVaiTro)) $errors[] = 'Tên vai trò không được bỏ trống';
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if ($id) {
            $exists = DB::table('vai_tro')->where('ma_vai_tro', $maVaiTro)->where('id', '!=', $id)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Mã vai trò đã tồn tại!');
            }

            DB::table('vai_tro')->where('id', $id)->update([
                'ma_vai_tro' => $maVaiTro,
                'ten_vai_tro' => $tenVaiTro,
                'updated_at' => now(),
            ]);
            $msg = 'Cập nhật vai trò thành công!';
        } else {
            $exists = DB::table('vai_tro')->where('ma_vai_tro', $maVaiTro)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Mã vai trò đã tồn tại!');
            }

            DB::table('vai_tro')->insert([
                'ma_vai_tro' => $maVaiTro,
                'ten_vai_tro' => $tenVaiTro,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $msg = 'Thêm mới vai trò thành công!';
        }

        return Response::Success([], $msg);
    }

    public function deleteVaiTro($id)
    {
        if (!$this->checkUserPermission('PhanQuyenController.deleteVaiTro')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $vaiTro = DB::table('vai_tro')->where('id', $id)->first();
        if (!$vaiTro) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy vai trò!');
        }

        if ($vaiTro->ma_vai_tro === 'admin') {
            return Response::Error('Lỗi thao tác', 'Không thể xóa vai trò admin hệ thống!');
        }

        DB::table('vai_tro')->where('id', $id)->delete();

        return Response::Success([], 'Xóa vai trò thành công!');
    }

    public function getDanhSachQuyen()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachQuyen')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $dsQuyen = DB::table('quyen_han')
            ->where('ma_quyen', 'not like', 'router-%')
            ->select('id', 'ma_quyen', 'ten_quyen', 'created_at', 'updated_at')
            ->orderBy('ma_quyen', 'asc')
            ->get();

        return Response::Success($dsQuyen, 'Lấy danh sách quyền hạn thành công');
    }

    public function putQuyen(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.putQuyen')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $maQuyen = trim($request->input('ma_quyen', ''));
        $tenQuyen = trim($request->input('ten_quyen', ''));

        $errors = [];
        if (empty($maQuyen)) $errors[] = 'Mã quyền không được bỏ trống';
        if (empty($tenQuyen)) $errors[] = 'Tên quyền không được bỏ trống';
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        if ($id) {
            $exists = DB::table('quyen_han')->where('ma_quyen', $maQuyen)->where('id', '!=', $id)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Mã quyền đã tồn tại!');
            }

            DB::table('quyen_han')->where('id', $id)->update([
                'ma_quyen' => $maQuyen,
                'ten_quyen' => $tenQuyen,
                'updated_at' => now(),
            ]);
            $msg = 'Cập nhật quyền thành công!';
        } else {
            $exists = DB::table('quyen_han')->where('ma_quyen', $maQuyen)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Mã quyền đã tồn tại!');
            }

            DB::table('quyen_han')->insert([
                'ma_quyen' => $maQuyen,
                'ten_quyen' => $tenQuyen,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $msg = 'Thêm quyền mới thành công!';
        }

        return Response::Success([], $msg);
    }

    public function deleteQuyen($id)
    {
        if (!$this->checkUserPermission('PhanQuyenController.deleteQuyen')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $deleted = DB::table('quyen_han')->where('id', $id)->delete();
        if ($deleted === 0) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy quyền hạn để xóa!');
        }

        return Response::Success([], 'Xóa quyền hạn thành công!');
    }

    public function getMaTranQuyen()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getMaTranQuyen')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $dsVaiTro = DB::table('vai_tro')->orderBy('id', 'asc')->get();
        $dsQuyen = DB::table('quyen_han')->where('ma_quyen', 'not like', 'router-%')->orderBy('ma_quyen', 'asc')->get();
        $vaiTroQuyen = DB::table('vai_tro_quyen')->get();

        $mapping = [];
        foreach ($vaiTroQuyen as $item) {
            if (!isset($mapping[$item->vai_tro_id])) {
                $mapping[$item->vai_tro_id] = [];
            }
            $mapping[$item->vai_tro_id][] = $item->quyen_id;
        }

        return Response::Success([
            'ds_vai_tro' => $dsVaiTro,
            'ds_quyen' => $dsQuyen,
            'mapping' => $mapping
        ], 'Lấy ma trận quyền thành công');
    }

    public function updateQuyenVaiTro(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.updateQuyenVaiTro')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $vaiTroId = $request->input('vai_tro_id');
        $quyenIds = $request->input('quyen_ids', []);

        if (!$vaiTroId || !is_array($quyenIds)) {
            return Response::Error('Sai định dạng dữ liệu', 'Dữ liệu vai_tro_id hoặc quyen_ids không hợp lệ!');
        }

        DB::transaction(function () use ($vaiTroId, $quyenIds) {
            DB::table('vai_tro_quyen')->where('vai_tro_id', $vaiTroId)->delete();

            $data = [];
            foreach ($quyenIds as $qId) {
                $data[] = [
                    'vai_tro_id' => $vaiTroId,
                    'quyen_id' => $qId,
                ];
            }
            if (!empty($data)) {
                DB::table('vai_tro_quyen')->insert($data);
            }
        });

        return Response::Success([], 'Cập nhật phân quyền vai trò thành công!');
    }

    public function getDanhSachNguoiDung(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachNguoiDung')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $userType = $request->query('user_type', 'giang_vien');
        $search = $request->query('search', '');

        if ($userType === 'sinh_vien') {
            $query = DB::table('sinh_vien')
                ->select('id as user_id', 'ho_ten', 'email');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ho_ten', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
        } else {
            $userType = 'giang_vien';
            $query = DB::table('giang_vien')
                ->select('id_giang_vien as user_id', 'ho_ten', 'email');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ho_ten', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
        }

        $users = $query->limit(50)->get();

        $userEmails = $users->pluck('email')->filter()->toArray();
        $tkList = DB::table('tai_khoan')->whereIn('email', $userEmails)->get()->keyBy('email');
        $tkIds = $tkList->pluck('id_tai_khoan')->toArray();

        $userRoles = [];
        if (!empty($tkIds)) {
            $userRoles = DB::table('quyen_nhom_tai_khoan')
                ->join('quyen_nhom', 'quyen_nhom_tai_khoan.id_quyen_nhom', '=', 'quyen_nhom.id_quyen_nhom')
                ->whereIn('quyen_nhom_tai_khoan.id_tai_khoan', $tkIds)
                ->select('quyen_nhom_tai_khoan.id_tai_khoan', 'quyen_nhom.id_quyen_nhom as vai_tro_id', 'quyen_nhom.tieu_de as ten_vai_tro')
                ->get()
                ->groupBy('id_tai_khoan');
        }

        foreach ($users as $u) {
            $tk = $tkList->get($u->email);
            $tkId = $tk ? $tk->id_tai_khoan : null;
            $u->roles = ($tkId && isset($userRoles[$tkId])) ? $userRoles[$tkId]->toArray() : [];
        }

        return Response::Success($users, 'Lấy danh sách người dùng thành công');
    }

    public function putVaiTroNguoiDung(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.putVaiTroNguoiDung')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $userId = $request->input('user_id');
        $userType = $request->input('user_type');
        $vaiTroIds = $request->input('vai_tro_ids', []);
        $email = $request->input('email', '');

        $errors = [];
        if (!$userId) $errors[] = 'user_id không được bỏ trống';
        if (!is_array($vaiTroIds)) $errors[] = 'vai_tro_ids phải là mảng';
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        DB::transaction(function () use ($userId, $userType, $vaiTroIds, $email) {
            if (!empty($email)) {
                $tk = DB::table('tai_khoan')->where('email', $email)->first();
                if (!$tk) {
                    $idTaiKhoan = DB::table('tai_khoan')->insertGetId([
                        'email' => $email,
                        'ho_ten' => $email,
                        'ngay_tao' => now()
                    ]);
                } else {
                    $idTaiKhoan = $tk->id_tai_khoan;
                }

                DB::table('quyen_nhom_tai_khoan')->where('id_tai_khoan', $idTaiKhoan)->delete();

                $data = [];
                foreach ($vaiTroIds as $vtId) {
                    $data[] = [
                        'id_tai_khoan' => $idTaiKhoan,
                        'id_quyen_nhom' => $vtId
                    ];
                }
                if (!empty($data)) {
                    DB::table('quyen_nhom_tai_khoan')->insert($data);
                }
            }
        });

        return Response::Success([], 'Cập nhật nhóm quyền người dùng thành công!');
    }

    public function getCaiDat()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getCaiDat')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $caiDatList = DB::table('cai_dat')->orderBy('id', 'asc')->get();
        $dsVaiTro = DB::table('vai_tro')->select('id', 'ma_vai_tro', 'ten_vai_tro')->orderBy('ten_vai_tro', 'asc')->get();

        return Response::Success([
            'cai_dat' => $caiDatList,
            'ds_vai_tro' => $dsVaiTro
        ], 'Lấy danh sách cài đặt thành công');
    }

    public function putCaiDat(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.putCaiDat')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $id = $request->input('id');
        $tieuDe = $request->input('tieu_de');
        $danhMuc = $request->input('danh_muc');
        $khoa = trim($request->input('khoa', ''));
        $kieuDuLieu = $request->input('kieu_du_lieu', 'Text');
        $giaTri = $request->input('gia_tri');
        $phamVi = $request->input('pham_vi', 'Cục bộ');
        $moTa = $request->input('mo_ta');

        if (empty($khoa)) {
            return Response::Error('Sai định dạng dữ liệu', 'Mã biến cài đặt (khoa) không được bỏ trống!');
        }

        $saveData = [
            'tieu_de' => $tieuDe,
            'danh_muc' => $danhMuc,
            'khoa' => $khoa,
            'kieu_du_lieu' => $kieuDuLieu,
            'gia_tri' => $giaTri,
            'pham_vi' => $phamVi,
            'mo_ta' => $moTa,
            'updated_at' => now(),
        ];

        if ($id) {
            $exists = DB::table('cai_dat')->where('khoa', $khoa)->where('id', '!=', $id)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Tên biến cài đặt đã tồn tại!');
            }

            DB::table('cai_dat')->where('id', $id)->update($saveData);
            $msg = 'Cập nhật biến cài đặt thành công!';
        } else {
            $exists = DB::table('cai_dat')->where('khoa', $khoa)->exists();
            if ($exists) {
                return Response::Error('Trùng dữ liệu', 'Tên biến cài đặt đã tồn tại!');
            }

            $saveData['created_at'] = now();
            DB::table('cai_dat')->insert($saveData);
            $msg = 'Thêm mới biến cài đặt thành công!';
        }

        return Response::Success([], $msg);
    }

    public function deleteCaiDat($id)
    {
        if (!$this->checkUserPermission('PhanQuyenController.deleteCaiDat')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $deleted = DB::table('cai_dat')->where('id', $id)->delete();
        if ($deleted === 0) {
            return Response::Error('Không tìm thấy', 'Không tìm thấy biến cài đặt để xóa!');
        }

        return Response::Success([], 'Xóa biến cài đặt thành công!');
    }
}
