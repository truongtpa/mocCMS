<?php

namespace App\Console\Commands;

use App\Jobs\sendEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TaiKhoanChucVu;

class sendEmailTB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-email-tb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $listMailData = DB::table('van_ban_tiep_nhan')
            ->leftJoin('van_ban', 'van_ban.id_van_ban', '=', 'van_ban_tiep_nhan.id_van_ban')
            ->leftJoin('tai_khoan','tai_khoan.id_tai_khoan','=','van_ban_tiep_nhan.id_tai_khoan')
            ->where('van_ban_tiep_nhan.so_lan_thong_bao', '=', 0)
            ->select(
                'van_ban_tiep_nhan.id_van_ban',
                'van_ban_tiep_nhan.id_tai_khoan',
                'tai_khoan.ho_ten',
                'van_ban_tiep_nhan.phu_trach_chinh',
                'tai_khoan.email'
            )->get();

        foreach ($listMailData as $mailData) {
            $vanBan = DB::table('van_ban')
                ->join('loai_van_ban', 'van_ban.id_loai_van_ban', '=', 'loai_van_ban.id_loai_van_ban')
                ->where('van_ban.id_van_ban', $mailData->id_van_ban)
                ->select(
                    'van_ban.*',
                    'loai_van_ban.ten_loai_van_ban'
                )
                ->first();

            if (!$vanBan) {
                continue;
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
                continue;
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



            $subject = 'Xử lý văn bản số ' . ($vanBan->so_van_ban ?? '') . ' - ' . ($vanBan->tieu_de ?? '');
            $data = [
                'id_tai_khoan' =>  $mailData->id_tai_khoan,
                'id_van_ban' => $mailData->id_van_ban,
                'template' => 'emails.van-ban-moi',
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
        }
        return self::SUCCESS;
    }
}
