<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhanQuyenController extends Controller
{
    /**
     * Helper kiểm tra quyền của tài khoản hiện tại
     */
    private function checkUserPermission($permissionKey)
    {
        $userId = session()->get(\App\VLUTE::SESSION_IDTaiKhoan);
        $email = session()->get(\App\VLUTE::SESSION_Email);
        if (!$userId || !$email) return false;

        $isStudent = str_contains($email, 'student.vlute.edu.vn') || str_contains($email, 'st.vlute.edu.vn');
        $userType = $isStudent ? 'sinh_vien' : 'giang_vien';

        // Kiểm tra nếu là Admin hệ thống
        $isAdmin = DB::table('vai_tro_nguoi_dung')
            ->join('vai_tro', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro.id')
            ->where('vai_tro_nguoi_dung.user_id', $userId)
            ->where('vai_tro_nguoi_dung.user_type', $userType)
            ->where('vai_tro.ma_vai_tro', 'admin')
            ->exists();

        if ($isAdmin) return true;

        // Kiểm tra quyền hạn tương ứng được gán
        return DB::table('vai_tro_nguoi_dung')
            ->join('vai_tro_quyen', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro_quyen.vai_tro_id')
            ->join('quyen_han', 'vai_tro_quyen.quyen_id', '=', 'quyen_han.id')
            ->where('vai_tro_nguoi_dung.user_id', $userId)
            ->where('vai_tro_nguoi_dung.user_type', $userType)
            ->where('quyen_han.ma_quyen', $permissionKey)
            ->exists();
    }

    /**
     * Lấy danh sách tất cả các vai trò
     */
    public function getDanhSachVaiTro()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachVaiTro')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        try {
            $dsVaiTro = DB::table('vai_tro')
                ->select('id', 'ma_vai_tro', 'ten_vai_tro', 'created_at', 'updated_at')
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $dsVaiTro
            ]);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@getDanhSachVaiTro Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Lỗi máy chủ khi lấy danh sách vai trò'], 500);
        }
    }

    /**
     * Thêm mới hoặc cập nhật vai trò
     */
    public function luuVaiTro(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.luuVaiTro')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        $request->validate([
            'ma_vai_tro' => 'required|string|max:50',
            'ten_vai_tro' => 'required|string|max:100',
        ]);

        try {
            $id = $request->input('id');
            $maVaiTro = trim($request->input('ma_vai_tro'));
            $tenVaiTro = trim($request->input('ten_vai_tro'));

            if ($id) {
                $exists = DB::table('vai_tro')->where('ma_vai_tro', $maVaiTro)->where('id', '!=', $id)->exists();
                if ($exists) {
                    return response()->json(['status' => 400, 'message' => 'Mã vai trò đã tồn tại!'], 400);
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
                    return response()->json(['status' => 400, 'message' => 'Mã vai trò đã tồn tại!'], 400);
                }

                DB::table('vai_tro')->insert([
                    'ma_vai_tro' => $maVaiTro,
                    'ten_vai_tro' => $tenVaiTro,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $msg = 'Thêm mới vai trò thành công!';
            }

            return response()->json(['status' => 200, 'message' => $msg]);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@luuVaiTro Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Lưu vai trò thất bại'], 500);
        }
    }

    /**
     * Xóa vai trò
     */
    public function xoaVaiTro($id)
    {
        if (!$this->checkUserPermission('PhanQuyenController.xoaVaiTro')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        try {
            $vaiTro = DB::table('vai_tro')->where('id', $id)->first();
            if (!$vaiTro) {
                return response()->json(['status' => 404, 'message' => 'Không tìm thấy vai trò!'], 404);
            }

            if ($vaiTro->ma_vai_tro === 'admin') {
                return response()->json(['status' => 400, 'message' => 'Không thể xóa vai trò admin hệ thống!'], 400);
            }

            DB::table('vai_tro')->where('id', $id)->delete();

            return response()->json(['status' => 200, 'message' => 'Xóa vai trò thành công!']);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@xoaVaiTro Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Xóa vai trò thất bại'], 500);
        }
    }

    /**
     * Lấy danh sách quyền hạn
     */
    public function getDanhSachQuyen()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachQuyen')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        try {
            $dsQuyen = DB::table('quyen_han')
                ->where('ma_quyen', 'not like', 'router-%')
                ->select('id', 'ma_quyen', 'ten_quyen', 'created_at', 'updated_at')
                ->orderBy('ma_quyen', 'asc')
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $dsQuyen
            ]);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@getDanhSachQuyen Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Lỗi máy chủ khi lấy danh sách quyền'], 500);
        }
    }

    /**
     * Thêm mới hoặc cập nhật quyền hạn
     */
    public function luuQuyen(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.luuQuyen')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        $request->validate([
            'ma_quyen' => 'required|string|max:100',
            'ten_quyen' => 'required|string|max:150',
        ]);

        try {
            $id = $request->input('id');
            $maQuyen = trim($request->input('ma_quyen'));
            $tenQuyen = trim($request->input('ten_quyen'));

            if ($id) {
                $exists = DB::table('quyen_han')->where('ma_quyen', $maQuyen)->where('id', '!=', $id)->exists();
                if ($exists) {
                    return response()->json(['status' => 400, 'message' => 'Mã quyền đã tồn tại!'], 400);
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
                    return response()->json(['status' => 400, 'message' => 'Mã quyền đã tồn tại!'], 400);
                }

                DB::table('quyen_han')->insert([
                    'ma_quyen' => $maQuyen,
                    'ten_quyen' => $tenQuyen,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $msg = 'Thêm quyền mới thành công!';
            }

            return response()->json(['status' => 200, 'message' => $msg]);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@luuQuyen Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Lưu quyền thất bại'], 500);
        }
    }

    /**
     * Xóa quyền hạn
     */
    public function xoaQuyen($id)
    {
        if (!$this->checkUserPermission('PhanQuyenController.xoaQuyen')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        try {
            DB::table('quyen_han')->where('id', $id)->delete();
            return response()->json(['status' => 200, 'message' => 'Xóa quyền hạn thành công!']);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@xoaQuyen Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Xóa quyền thất bại'], 500);
        }
    }

    /**
     * Lấy ma trận quyền - vai trò
     */
    public function getMaTranQuyen()
    {
        if (!$this->checkUserPermission('PhanQuyenController.getMaTranQuyen')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        try {
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

            return response()->json([
                'status' => 200,
                'data' => [
                    'ds_vai_tro' => $dsVaiTro,
                    'ds_quyen' => $dsQuyen,
                    'mapping' => $mapping
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@getMaTranQuyen Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Lỗi máy chủ khi lấy ma trận quyền'], 500);
        }
    }

    /**
     * Cập nhật gán quyền cho vai trò
     */
    public function capNhatQuyenVaiTro(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.capNhatQuyenVaiTro')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        $request->validate([
            'vai_tro_id' => 'required|integer',
            'quyen_ids' => 'present|array',
        ]);

        try {
            $vaiTroId = $request->input('vai_tro_id');
            $quyenIds = $request->input('quyen_ids', []);

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

            return response()->json(['status' => 200, 'message' => 'Cập nhật phân quyền vai trò thành công!']);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@capNhatQuyenVaiTro Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Cập nhật phân quyền vai trò thất bại'], 500);
        }
    }

    /**
     * Tìm kiếm và lấy danh sách người dùng kèm vai trò
     */
    public function getDanhSachNguoiDung(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.getDanhSachNguoiDung')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        try {
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

            $userIds = $users->pluck('user_id')->toArray();
            $userRoles = DB::table('vai_tro_nguoi_dung')
                ->join('vai_tro', 'vai_tro_nguoi_dung.vai_tro_id', '=', 'vai_tro.id')
                ->whereIn('vai_tro_nguoi_dung.user_id', $userIds)
                ->where('vai_tro_nguoi_dung.user_type', $userType)
                ->select('vai_tro_nguoi_dung.user_id', 'vai_tro.id as vai_tro_id', 'vai_tro.ten_vai_tro', 'vai_tro.ma_vai_tro')
                ->get()
                ->groupBy('user_id');

            foreach ($users as $u) {
                $u->roles = isset($userRoles[$u->user_id]) ? $userRoles[$u->user_id]->toArray() : [];
            }

            return response()->json([
                'status' => 200,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@getDanhSachNguoiDung Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Lỗi máy chủ khi lấy danh sách người dùng'], 500);
        }
    }

    /**
     * Gán vai trò cho người dùng
     */
    public function ganVaiTroNguoiDung(Request $request)
    {
        if (!$this->checkUserPermission('PhanQuyenController.ganVaiTroNguoiDung')) {
            return response()->json(['status' => 403, 'message' => 'Bạn không có quyền thực hiện thao tác này!'], 403);
        }

        $request->validate([
            'user_id' => 'required|integer',
            'user_type' => 'required|string|in:sinh_vien,giang_vien',
            'vai_tro_ids' => 'present|array',
        ]);

        try {
            $userId = $request->input('user_id');
            $userType = $request->input('user_type');
            $vaiTroIds = $request->input('vai_tro_ids', []);

            DB::transaction(function () use ($userId, $userType, $vaiTroIds) {
                DB::table('vai_tro_nguoi_dung')
                    ->where('user_id', $userId)
                    ->where('user_type', $userType)
                    ->delete();

                $data = [];
                foreach ($vaiTroIds as $vtId) {
                    $data[] = [
                        'user_id' => $userId,
                        'user_type' => $userType,
                        'vai_tro_id' => $vtId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($data)) {
                    DB::table('vai_tro_nguoi_dung')->insert($data);
                }
            });

            return response()->json(['status' => 200, 'message' => 'Cập nhật vai trò người dùng thành công!']);
        } catch (\Exception $e) {
            Log::error('PhanQuyenController@ganVaiTroNguoiDung Error: ' . $e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Gán vai trò cho người dùng thất bại'], 500);
        }
    }
}
