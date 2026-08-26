<?php

namespace App\Console\Commands;

use App\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Jobs\sendEmail;
use App\Models\TaiKhoanChucVu;
class CreateMailReminder extends Command
{
    protected $signature = 'app:create-mail-reminder';
    protected $description = 'Kiểm tra văn bản gần đến hạn xử lý và chưa được xử lý';

    public function handle()
    {
        DB::table('van_ban')
            ->whereNotNull('ngay_phan_hoi')
            ->where('ngay_phan_hoi', '<', now()->toDateString())
            ->update(['trang_thai' => 5]);

        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $nguong_het_han = $now->copy()->addDays(3);


        $listMailData = DB::table('van_ban_tiep_nhan')
            ->leftJoin('van_ban', 'van_ban.id_van_ban', '=', 'van_ban_tiep_nhan.id_van_ban')
            ->leftJoin('tai_khoan','tai_khoan.id_tai_khoan','=','van_ban_tiep_nhan.id_tai_khoan')
            ->where('trang_thai','=', 2)
            ->whereDate('ngay_phan_hoi', '<=', $nguong_het_han)
            ->select(
                'van_ban_tiep_nhan.id_van_ban',
                'van_ban_tiep_nhan.id_tai_khoan',
                'tai_khoan.ho_ten',
                'tai_khoan.email'
            )->get();

        foreach ($listMailData as $mailData) {
            $vanBan = DB::table('van_ban')
                ->join('loai_van_ban', 'van_ban.id_loai_van_ban', '=', 'loai_van_ban.id_loai_van_ban')
                ->where('van_ban.id_van_ban', $mailData->id_van_ban)
                ->select(
                    'van_ban.*',
                    'loai_van_ban.ten_loai_van_ban'
                )->first();

            if (!$vanBan) {
                return self::SUCCESS;
            }

            $dsNguoiThucHien = TaiKhoanChucVu::joinChucVuChinh(
                DB::table('van_ban_tiep_nhan')
                    ->join('tai_khoan', 'van_ban_tiep_nhan.id_tai_khoan', '=', 'tai_khoan.id_tai_khoan'),
                'tai_khoan', null, 'don_vi'
            )
                ->where('van_ban_tiep_nhan.id_van_ban', $mailData->id_van_ban)
                ->select(
                    'van_ban_tiep_nhan.*',
                    'tai_khoan.ho_ten',
                    'tai_khoan.email',
                    'don_vi.ten_don_vi'
                )->get();

            if (!$dsNguoiThucHien) {
                return self::SUCCESS;
            }

            $dsFiles = DB::table('files')
                ->where('id_van_ban', $mailData->id_van_ban)
                ->select('ten_file', 'path_s3', 'dung_luong', 'type')
                ->get()
                ->map(function ($item) {
                    $s3Service = new \App\Services\S3Services();
                    $item->full_path = $item->path_s3
                        ? $s3Service->taoPresignedUrl($item->path_s3, fileName: $item->ten_file)
                        : null;
                    return $item;
                });

            $year = $vanBan->ngay_phan_hoi ? Carbon::parse($vanBan->ngay_phan_hoi)->year : '';
            $nam_ban_hanh = $vanBan->ngay_ky ? Carbon::parse($vanBan->ngay_ky)->year : '';
            $ngayPhanHoiCarbon = $vanBan->ngay_phan_hoi
                ? Carbon::parse($vanBan->ngay_phan_hoi)->setTimezone('Asia/Ho_Chi_Minh')->endOfDay()
                : null;
            $ngayPhanHoi = $ngayPhanHoiCarbon ? $ngayPhanHoiCarbon->format('d/m/Y') : '';
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $soGio = round($ngayPhanHoiCarbon ? $now->diffInHours($ngayPhanHoiCarbon, false) : 0);
            $soNgayConLai = round($soGio / 24);
            $chuoiConLai = '';
            if ($soNgayConLai > 0) {
                $chuoiConLai = "(còn lại " . abs($soNgayConLai) . " ngày)";
            } elseif ($soNgayConLai < 0) {
                $chuoiConLai = "(quá hạn " . abs($soNgayConLai) . " ngày)";
            } elseif ($soNgayConLai == 0 || $soNgayConLai == -0) {
                $chuoiConLai = $soGio > 0
                    ? "(còn lại " . abs($soGio) . " giờ)"
                    : "(quá hạn " . abs($soGio) . " giờ)";
            }

            $subject = '[Nhắc nhở] Xử lý văn bản số ' . ($vanBan->so_van_ban ?? '') . ' - ' . ($vanBan->tieu_de ?? '');
            $data = [
                'id_tai_khoan' =>  $mailData->id_tai_khoan,
                'id_van_ban' => $mailData->id_van_ban,
                'template' => 'emails.nhac-lich',
                'subject' => $subject,
                'title' => $subject,
                'email' =>  $mailData->email,
                'nguoiNhan' => $mailData,
                'vbChiTiet' => $vanBan,
                'dsNguoiThucHien'  => $dsNguoiThucHien,
                'dsFiles' => $dsFiles,
            ];

            $reminderJobs = new SendEmail($data);
            dispatch($reminderJobs->onQueue('default')->delay(Carbon::now()->addSecond(1)));
            return self::SUCCESS;
        }

        return self::SUCCESS;
    }
}
