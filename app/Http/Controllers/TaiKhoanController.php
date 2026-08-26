<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\TaiKhoanChucVu;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaiKhoanController extends Controller
{
    public function danhSach(Request $request)
    {
        $perPage = intval(env('ITEM_PER_PAGE'));
        $keyword = $request->input('s', '');

        $ds = TaiKhoanChucVu::joinChucVuChinh(TaiKhoan::query(), 'tai_khoan', 'chuc_vu', 'don_vi')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('tai_khoan.ho_ten', 'like', "%{$keyword}%")
                    ->orWhere('tai_khoan.email', 'like', "%{$keyword}%");
            })
            ->select(
                'tai_khoan.*',
                'tkcv_chinh.id_don_vi',
                'don_vi.ten_don_vi',
                'chuc_vu.ten_chuc_vu',
            )
            ->orderBy('tai_khoan.ho_ten')
            ->paginate($perPage);

        // Kèm toàn bộ chức vụ theo từng đơn vị để hiển thị và quản lý ngay trên bảng.
        $dsChucVu = TaiKhoanChucVu::theoTaiKhoan($ds->pluck('id_tai_khoan')->all());
        $ds->getCollection()->transform(function ($taiKhoan) use ($dsChucVu) {
            $taiKhoan->chuc_vu = $dsChucVu->get($taiKhoan->id_tai_khoan, collect())->values();

            return $taiKhoan;
        });

        return Response::Success($ds, '');
    }

    public function getDsTaiKhoan(Request $request)
    {
        $keyword = $request->input('s', '');
        $ds = TaiKhoanChucVu::joinChucVuChinh(TaiKhoan::query(), 'tai_khoan', 'chuc_vu', 'don_vi')
            ->select(
                'tai_khoan.id_tai_khoan',
                'tai_khoan.email',
                'tai_khoan.ho_ten',
                'tkcv_chinh.id_don_vi',
                'don_vi.ten_don_vi',
                'chuc_vu.id_chuc_vu',
                'chuc_vu.ten_chuc_vu',
                'chuc_vu.truong_don_vi',
                DB::raw("COALESCE(chuc_vu.ten_chuc_vu, 'Chưa có chức vụ') as ten_chuc_vu"),
                DB::raw('COALESCE(chuc_vu.uu_tien, 9999) as uu_tien')
            )
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('tai_khoan.ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('tai_khoan.email', 'like', "%{$keyword}%")
                        ->orWhere('don_vi.ten_don_vi', 'like', "%{$keyword}%")
                        ->orWhere('chuc_vu.ten_chuc_vu', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('chuc_vu.uu_tien', 'asc')
            ->orderBy('tai_khoan.ho_ten', 'asc')
            ->when($request->integer('limit'), function ($query, $limit) {
                $query->limit(max(1, min(50, $limit)));
            })
            ->get();

        return Response::Success($ds);

    }

    public function them(Request $request)
    {
        $ho_ten = $request->ho_ten;
        $email = $request->email;

        $errors = [];

        if (empty($ho_ten)) {
            $errors[] = 'Họ tên không được bỏ trống';
        }

        if (empty($email)) {
            $errors[] = 'Email không được bỏ trống';
        } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không đúng định dạng';
        }

        if ($errors) {
            return Response::Error(
                'Sai định dạng dữ liệu',
                $errors
            );
        }

        $check = TaiKhoan::where('email', $email)->exists();

        if ($check) {
            return Response::Error(
                'Trùng dữ liệu',
                'Email này đã tồn tại'
            );
        }

        try {
            // Đơn vị và chức vụ quản lý trong tai_khoan_chuc_vu, không nhập trực tiếp nữa.
            $count = TaiKhoan::insert([
                'ho_ten' => $ho_ten,
                'email' => $email,
            ]);

            if ($count) {
                return Response::Success(
                    'Thành công',
                    'Thêm dữ liệu thành công'
                );
            }

            return Response::Error(
                'Lỗi',
                'Phát sinh lỗi'
            );
        } catch (\Throwable $e) {
            \Log::error('Lỗi thêm tài khoản: '.$e->getMessage());

            return Response::Error(
                'Lỗi',
                $e->getMessage()
            );
        }
    }

    public function xoa(Request $request)
    {
        $check = TaiKhoan::where('id_tai_khoan', $request->id_tai_khoan)->delete();
        if ($check) {
            return Response::Success('Thành công', 'Xóa dữ liệu thành công');
        }

        return Response::Error('Lỗi', 'Phát sinh lỗi');
    }

    public function capNhat(Request $r)
    {
        $ho_ten = $r->ho_ten;
        $email = $r->email;
        $id_tai_khoan = $r->id_tai_khoan;

        $errors = [];

        if (empty($ho_ten)) {
            $errors[] = 'Họ tên không được bỏ trống';
        }

        if (empty($email)) {
            $errors[] = 'Email không được bỏ trống';
        } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không đúng định dạng';
        }

        if ($errors) {
            return Response::Error(
                'Sai định dạng dữ liệu',
                $errors
            );
        }

        $taiKhoan = TaiKhoan::where(
            'id_tai_khoan',
            $id_tai_khoan
        )->first();

        if (! $taiKhoan) {
            return Response::Error(
                'Lỗi',
                'Không tìm thấy tài khoản'
            );
        }

        try {
            $taiKhoan->ho_ten = $ho_ten;
            $taiKhoan->email = $email;
            $taiKhoan->save();

            return Response::Success(
                'Thành công',
                'Cập nhật dữ liệu thành công'
            );
        } catch (\Throwable $e) {
            \Log::error('Lỗi cập nhật tài khoản', [
                'id_tai_khoan' => $id_tai_khoan,
                'error' => $e->getMessage(),
            ]);

            return Response::Error(
                'Lỗi',
                $e->getMessage()
            );
        }
    }

    /** Chức vụ theo đơn vị của một tài khoản, kèm danh mục để chọn thêm. */
    public function getChucVu(Request $request)
    {
        if ($loi = $this->kiemTraDuLieu($request, [
            'id_tai_khoan' => ['required', 'integer', 'exists:tai_khoan,id_tai_khoan'],
        ], [
            'id_tai_khoan.required' => 'Vui lòng chọn tài khoản',
            'id_tai_khoan.exists' => 'Tài khoản không tồn tại',
        ])) {
            return $loi;
        }

        $idTaiKhoan = $request->integer('id_tai_khoan');

        return Response::Success([
            'tai_khoan' => TaiKhoan::find($idTaiKhoan),
            'danh_sach' => TaiKhoanChucVu::theoTaiKhoan([$idTaiKhoan])->get($idTaiKhoan, collect())->values(),
            'don_vi' => DB::table('don_vi')->select('id_don_vi', 'ten_don_vi')->orderBy('ten_don_vi')->get(),
            'chuc_vu' => DB::table('chuc_vu')->select('id_chuc_vu', 'ten_chuc_vu', 'truong_don_vi')
                ->orderByRaw('COALESCE(uu_tien, 9999)')->get(),
        ], '');
    }

    /** Gán một chức vụ tại một đơn vị cho tài khoản. */
    public function themChucVu(Request $request)
    {
        if ($loi = $this->kiemTraDuLieu($request, [
            'id_tai_khoan' => ['required', 'integer', 'exists:tai_khoan,id_tai_khoan'],
            'id_don_vi' => ['required', 'integer', 'exists:don_vi,id_don_vi'],
            'id_chuc_vu' => ['required', 'integer', 'exists:chuc_vu,id_chuc_vu'],
        ], [
            'id_don_vi.required' => 'Vui lòng chọn đơn vị',
            'id_chuc_vu.required' => 'Vui lòng chọn chức vụ',
        ])) {
            return $loi;
        }

        $idTaiKhoan = $request->integer('id_tai_khoan');
        $trung = TaiKhoanChucVu::where('id_tai_khoan', $idTaiKhoan)
            ->where('id_don_vi', $request->integer('id_don_vi'))
            ->where('id_chuc_vu', $request->integer('id_chuc_vu'))
            ->exists();
        if ($trung) {
            return Response::Error('Trùng dữ liệu', 'Tài khoản đã có chức vụ này tại đơn vị đã chọn');
        }

        TaiKhoanChucVu::create([
            'id_tai_khoan' => $idTaiKhoan,
            'id_don_vi' => $request->integer('id_don_vi'),
            'id_chuc_vu' => $request->integer('id_chuc_vu'),
            'ngay_tao' => now(),
        ]);

        return Response::Success(
            TaiKhoanChucVu::theoTaiKhoan([$idTaiKhoan])->get($idTaiKhoan, collect())->values(),
            'Thêm chức vụ thành công'
        );
    }

    /** Gỡ một chức vụ khỏi tài khoản. */
    public function xoaChucVu(Request $request)
    {
        if ($loi = $this->kiemTraDuLieu($request, [
            'id_tai_khoan_chuc_vu' => ['required', 'integer', 'exists:tai_khoan_chuc_vu,id_tai_khoan_chuc_vu'],
        ])) {
            return $loi;
        }

        $dong = TaiKhoanChucVu::find($request->integer('id_tai_khoan_chuc_vu'));
        $idTaiKhoan = (int) $dong->id_tai_khoan;
        $dong->delete();

        return Response::Success(
            TaiKhoanChucVu::theoTaiKhoan([$idTaiKhoan])->get($idTaiKhoan, collect())->values(),
            'Đã gỡ chức vụ khỏi tài khoản'
        );
    }
}
