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

        $dsVaiTro = DB::table('quyen_nhom')
            ->select('id_quyen_nhom as id', 'ma_vai_tro', 'tieu_de as ten_vai_tro', 'mac_dinh', 'ngay_tao', 'ngay_cap_nhat')
            ->orderBy('id_quyen_nhom', 'asc')
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
        if (empty($tenVaiTro)) $errors[] = 'Tên vai trò không được bỏ trống';
        if ($errors) {
            return Response::Error('Sai định dạng dữ liệu', $errors);
        }

        $data = [
            'ma_vai_tro' => $maVaiTro ?: null,
            'tieu_de' => $tenVaiTro,
            'ngay_cap_nhat' => now(),
        ];

        if ($id) {
            DB::table('quyen_nhom')->where('id_quyen_nhom', $id)->update($data);
            $msg = 'Cập nhật vai trò thành công!';
        } else {
            $data['mac_dinh'] = 0;
            $data['ngay_tao'] = now();
            DB::table('quyen_nhom')->insert($data);
            $msg = 'Thêm mới vai trò thành công!';
        }

        return Response::Success([], $msg);
    }

    public function deleteVaiTro($id)
    {
        if (!$this->checkUserPermission('PhanQuyenController.deleteVaiTro')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        if ($id == 1) {
            return Response::Error('Lỗi thao tác', 'Không thể xóa vai trò Quản trị viên hệ thống!');
        }

        DB::table('quyen_nhom')->where('id_quyen_nhom', $id)->delete();
        DB::table('quyen_nhom_chi_tiet')->where('id_quyen_nhom', $id)->delete();
        DB::table('quyen_nhom_tai_khoan')->where('id_quyen_nhom', $id)->delete();

        return Response::Success([], 'Xóa vai trò thành công!');
    }

    public function getDanhSachQuyen()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachQuyen')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $dsQuyen = DB::table('quyen_chi_tiet')
            ->select('id_quyen_chi_tiet as id', 'tieu_de as ten_quyen', 'funcs as ma_quyen', 'ngay_tao', 'ngay_cap_nhat')
            ->orderBy('id_quyen_chi_tiet', 'asc')
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
            DB::table('quyen_chi_tiet')->where('id_quyen_chi_tiet', $id)->update([
                'funcs' => $maQuyen,
                'tieu_de' => $tenQuyen,
                'ngay_cap_nhat' => now(),
            ]);
            $msg = 'Cập nhật quyền thành công!';
        } else {
            DB::table('quyen_chi_tiet')->insert([
                'id_quyen' => 1,
                'tieu_de' => $tenQuyen,
                'funcs' => $maQuyen,
                'show_views' => $maQuyen,
                'ngay_tao' => now(),
                'ngay_cap_nhat' => now(),
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

        $deleted = DB::table('quyen_chi_tiet')->where('id_quyen_chi_tiet', $id)->delete();
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

        $dsVaiTro = DB::table('quyen_nhom')
            ->select('id_quyen_nhom as id', 'ma_vai_tro', 'tieu_de as ten_vai_tro')
            ->orderBy('id_quyen_nhom', 'asc')
            ->get();

        $dsQuyen = DB::table('quyen_chi_tiet')
            ->select('id_quyen_chi_tiet as id', 'tieu_de as ten_quyen', 'funcs as ma_quyen')
            ->orderBy('id_quyen_chi_tiet', 'asc')
            ->get();

        $vaiTroQuyen = DB::table('quyen_nhom_chi_tiet')->get();

        $mapping = [];
        foreach ($vaiTroQuyen as $item) {
            if (!isset($mapping[$item->id_quyen_nhom])) {
                $mapping[$item->id_quyen_nhom] = [];
            }
            $mapping[$item->id_quyen_nhom][] = $item->id_quyen_chi_tiet;
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
            DB::table('quyen_nhom_chi_tiet')->where('id_quyen_nhom', $vaiTroId)->delete();

            $data = [];
            foreach ($quyenIds as $qId) {
                $data[] = [
                    'id_quyen_nhom' => $vaiTroId,
                    'id_quyen_chi_tiet' => $qId,
                    'ngay_tao' => now(),
                    'ngay_cap_nhat' => now()
                ];
            }
            if (!empty($data)) {
                DB::table('quyen_nhom_chi_tiet')->insert($data);
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
                ->select('id as user_id', 'ho_ten', 'email');
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
        $tkIds = array_map('strval', $tkList->pluck('id')->toArray());

        $userRoles = [];
        if (!empty($tkIds)) {
            $allRoles = DB::table('quyen_nhom_tai_khoan')
                ->join('quyen_nhom', 'quyen_nhom_tai_khoan.id_quyen_nhom', '=', 'quyen_nhom.id_quyen_nhom')
                ->whereIn('quyen_nhom_tai_khoan.id_tai_khoan', $tkIds)
                ->select('quyen_nhom_tai_khoan.id_tai_khoan', 'quyen_nhom.id_quyen_nhom as vai_tro_id', 'quyen_nhom.tieu_de as ten_vai_tro')
                ->get();

            foreach ($allRoles as $r) {
                $userRoles[(string)$r->id_tai_khoan][] = [
                    'vai_tro_id' => $r->vai_tro_id,
                    'ten_vai_tro' => $r->ten_vai_tro
                ];
            }
        }

        foreach ($users as $u) {
            $tk = $tkList->get($u->email);
            $tkId = $tk ? (string)$tk->id : null;
            $u->roles = ($tkId && isset($userRoles[$tkId])) ? $userRoles[$tkId] : [];
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
        $email = trim($request->input('email', ''));

        if (empty($email) && $userId) {
            if ($userType === 'sinh_vien') {
                $email = DB::table('sinh_vien')->where('id', $userId)->value('email');
            } else {
                $email = DB::table('giang_vien')->where('id', $userId)->value('email');
            }
            if (empty($email)) {
                $email = DB::table('tai_khoan')->where('id', $userId)->value('email');
            }
        }

        $errors = [];
        if (!$userId && empty($email)) $errors[] = 'user_id hoặc email không được bỏ trống';
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
                    $idTaiKhoan = $tk->id;
                }

                DB::table('quyen_nhom_tai_khoan')->where('id_tai_khoan', (string)$idTaiKhoan)->delete();

                $data = [];
                foreach ($vaiTroIds as $vtId) {
                    $data[] = [
                        'id_tai_khoan' => (string)$idTaiKhoan,
                        'id_quyen_nhom' => $vtId,
                        'ngay_tao' => now(),
                        'ngay_cap_nhat' => now()
                    ];
                }
                if (!empty($data)) {
                    DB::table('quyen_nhom_tai_khoan')->insert($data);
                }
            }
        });

        return Response::Success([], 'Cập nhật vai trò người dùng thành công!');
    }

    public function getCaiDat()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getCaiDat')) {
            return Response::Error('Không có quyền', 'Bạn không có quyền thực hiện thao tác này!');
        }

        $caiDatList = DB::table('cai_dat')->orderBy('id', 'asc')->get();
        $dsVaiTro = DB::table('quyen_nhom')->select('id_quyen_nhom as id', 'tieu_de as ten_vai_tro')->orderBy('tieu_de', 'asc')->get();

        return Response::Success([
            'cai_dat' => $caiDatList,
            'ds_vai_tro' => $dsVaiTro
        ], 'Lấy danh sách cài đặt thành công');
    }
}
