<?php

namespace Tests\Feature;

use App\Jobs\sendEmail;
use App\Models\TaiKhoanChucVu;
use App\VLUTE;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TrinhKyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_signing_module_reuses_van_ban_and_files_tables(): void
    {
        $this->assertTrue(Schema::hasColumn('trinh_ky', 'id_van_ban'));
        $this->assertTrue(Schema::hasColumn('files', 'loai_file'));
        $this->assertTrue(Schema::hasTable('trinh_ky_cau_hinh_so'));
        $this->assertTrue(Schema::hasColumn('trinh_ky', 'id_trinh_ky_cau_hinh_so'));
        $this->assertTrue(Schema::hasColumn('loai_van_ban_ky_duyet', 'is_ban_giam_hieu'));
        $this->assertTrue(Schema::hasColumn('loai_van_ban_ky_duyet', 'thu_tu_uu_tien'));
        $this->assertFalse(Schema::hasColumn('loai_van_ban_ky_duyet', 'thu_tu'));
        $this->assertFalse(Schema::hasColumn('trinh_ky_duyet', 'thu_tu'));
        $this->assertTrue(Schema::hasTable('trinh_ky_cau_hinh_email'));
        $this->assertTrue(Schema::hasColumn('trinh_ky_cau_hinh_email', 'su_kien'));
        $this->assertTrue(Schema::hasTable('trinh_ky_da_xem'));
        $this->assertFalse(Schema::hasTable('trinh_ky_file'));
        $this->assertSame(0, DB::table('trinh_ky')->whereNull('id_van_ban')->count());

        foreach (['Báo cáo' => 'BC', 'Kế hoạch' => 'KH'] as $typeName => $code) {
            $typeId = DB::table('loai_van_ban')->where('ten_loai_van_ban', $typeName)->value('id_loai_van_ban');
            if ($typeId) {
                $this->assertDatabaseHas('trinh_ky_cau_hinh_so', [
                    'id_loai_van_ban' => $typeId,
                    'id_don_vi' => null,
                    'ky_hieu' => $code,
                    'duoi' => 'ĐHSPKTVL',
                    'is_mac_dinh' => 1,
                ]);
            }
        }
    }

    public function test_authenticated_account_can_open_signing_page_apis(): void
    {
        $accountId = DB::table('tai_khoan')->value('id_tai_khoan');

        if (! $accountId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa có tài khoản SSO.');
        }

        $session = [VLUTE::SESSION_IDTaiKhoan => $accountId];

        $this->withSession($session)
            ->getJson('/api/admin/trinh-ky/meta')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonStructure(['data' => ['tai_khoan', 'vai_tro', 'loai_van_ban', 'trang_thai']]);

        $this->withSession($session)
            ->getJson('/api/admin/trinh-ky')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonStructure(['data' => ['data', 'current_page', 'total']]);
    }

    public function test_create_request_saves_van_ban_before_file_metadata(): void
    {
        $accountId = DB::table('tai_khoan')->value('id_tai_khoan');
        $documentTypeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if (! $accountId || ! $documentTypeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa đủ tài khoản SSO hoặc loại văn bản.');
        }

        Mail::fake();
        $session = [VLUTE::SESSION_IDTaiKhoan => $accountId];
        $response = $this->withSession($session)->postJson('/api/admin/trinh-ky', [
            'id_loai_van_ban' => $documentTypeId,
            'id_nguoi_ky' => $accountId,
            'hinh_thuc_ky' => 'ky_chinh',
            'mo_ta' => 'Kiểm thử luồng lưu văn bản trước khi lưu file',
            'ngay_ban_hanh' => now()->toDateString(),
        ])->assertOk()->assertJsonPath('status', 200);

        $idVanBan = $response->json('data.id_van_ban');
        $idTrinhKy = $response->json('data.id_trinh_ky');
        $this->assertNotEmpty($idVanBan);
        $this->assertDatabaseHas('van_ban', [
            'id_van_ban' => $idVanBan,
            'tieu_de' => 'Kiểm thử luồng lưu văn bản trước khi lưu file',
        ]);
        $this->assertDatabaseHas('trinh_ky', [
            'id_trinh_ky' => $idTrinhKy,
            'id_van_ban' => $idVanBan,
        ]);

        $this->withSession($session)->postJson('/api/admin/trinh-ky/files', [
            'id_trinh_ky' => $idTrinhKy,
            'path_s3' => 'dev/tests/trinh-ky.docx',
            'ten_file' => 'trinh-ky.docx',
            'dung_luong' => '1 KB',
            'type' => 'docx',
            'loai_file' => 'du_thao',
        ])->assertOk()->assertJsonPath('status', 200);

        $this->assertDatabaseHas('files', [
            'id_van_ban' => $idVanBan,
            'path_s3' => 'dev/tests/trinh-ky.docx',
            'loai_file' => 'du_thao',
        ]);
    }

    public function test_document_is_marked_as_seen_per_account_after_opening_detail(): void
    {
        $accountId = DB::table('tai_khoan')->value('id_tai_khoan');
        $documentTypeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if (! $accountId || ! $documentTypeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa đủ tài khoản hoặc loại văn bản.');
        }

        Mail::fake();
        $session = [VLUTE::SESSION_IDTaiKhoan => $accountId];
        $created = $this->withSession($session)->postJson('/api/admin/trinh-ky', [
            'id_loai_van_ban' => $documentTypeId,
            'id_nguoi_ky' => $accountId,
            'hinh_thuc_ky' => 'ky_chinh',
            'mo_ta' => 'Kiểm thử đánh dấu văn bản đã xem',
            'ngay_ban_hanh' => now()->toDateString(),
        ])->assertOk()->assertJsonPath('status', 200);
        $requestId = $created->json('data.id_trinh_ky');

        $before = $this->withSession($session)->getJson('/api/admin/trinh-ky')->assertOk();
        $beforeItem = collect($before->json('data.data'))->firstWhere('id_trinh_ky', $requestId);
        $this->assertSame(1, (int) ($beforeItem['chua_xem'] ?? 0));

        $this->withSession($session)->getJson('/api/admin/trinh-ky/chi-tiet?id_trinh_ky='.$requestId)
            ->assertOk()->assertJsonPath('status', 200);
        $this->assertDatabaseHas('trinh_ky_da_xem', ['id_trinh_ky' => $requestId, 'id_tai_khoan' => $accountId]);

        $after = $this->withSession($session)->getJson('/api/admin/trinh-ky')->assertOk();
        $afterItem = collect($after->json('data.data'))->firstWhere('id_trinh_ky', $requestId);
        $this->assertSame(0, (int) ($afterItem['chua_xem'] ?? 1));
    }

    public function test_administrative_office_can_select_account_for_signing_without_creating_duplicates(): void
    {
        $adminId = $this->administrativeOfficeAccountId();
        $signerId = DB::table('tai_khoan')->orderBy('id_tai_khoan')->value('id_tai_khoan');
        if (! $adminId || ! $signerId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa có tài khoản quản trị và người ký.');
        }

        $session = [VLUTE::SESSION_IDTaiKhoan => $adminId];
        $payload = [
            'id_loai_van_ban' => null,
            'id_tai_khoan' => $signerId,
            'hinh_thuc_ky' => 'ky_chinh',
            'ghi_chu' => 'Người ký dùng chung',
        ];

        $this->withSession($session)->postJson('/api/admin/trinh-ky/nguoi-ky', $payload)
            ->assertOk()->assertJsonPath('status', 200);
        $countAfterFirstSave = DB::table('loai_van_ban_ky_duyet')
            ->whereNull('id_loai_van_ban')->where('id_tai_khoan', $signerId)->count();

        $this->withSession($session)->postJson('/api/admin/trinh-ky/nguoi-ky', $payload)
            ->assertOk()->assertJsonPath('status', 200);
        $countAfterSecondSave = DB::table('loai_van_ban_ky_duyet')
            ->whereNull('id_loai_van_ban')->where('id_tai_khoan', $signerId)->count();

        $this->assertSame($countAfterFirstSave, $countAfterSecondSave);
        $this->assertGreaterThan(0, $countAfterSecondSave);
    }

    public function test_administrative_office_can_configure_and_use_document_number_format(): void
    {
        $adminId = $this->administrativeOfficeAccountId();
        $typeId = DB::table('loai_van_ban')->where('ten_loai_van_ban', 'Báo cáo')->value('id_loai_van_ban');
        if (! $adminId || ! $typeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa có tài khoản quản trị hoặc loại Báo cáo.');
        }

        Mail::fake();
        $session = [VLUTE::SESSION_IDTaiKhoan => $adminId];
        $numberConfigResponse = $this->withSession($session)->postJson('/api/admin/trinh-ky/cau-hinh-so', [
            'id_loai_van_ban' => $typeId,
            'id_don_vi' => null,
            'ky_hieu' => 'BC',
            'duoi' => 'ĐHSPKTVL-KIEMTHU',
            'is_mac_dinh' => false,
        ])->assertOk()->assertJsonPath('status', 200);
        $numberConfigId = $numberConfigResponse->json('data.id_trinh_ky_cau_hinh_so');

        $signerResponse = $this->withSession($session)->postJson('/api/admin/trinh-ky/nguoi-ky', [
            'id_loai_van_ban' => $typeId,
            'id_tai_khoan' => $adminId,
            'hinh_thuc_ky' => 'ky_chinh',
        ])->assertOk()->assertJsonPath('status', 200);
        $signerConfigId = $signerResponse->json('data.id_loai_van_ban_ky_duyet');

        $requestResponse = $this->withSession($session)->postJson('/api/admin/trinh-ky', [
            'id_loai_van_ban' => $typeId,
            'id_nguoi_ky' => $adminId,
            'hinh_thuc_ky' => 'ky_chinh',
            'mo_ta' => 'Kiểm thử cấu hình ký hiệu số',
            'ngay_ban_hanh' => now()->toDateString(),
        ])->assertOk()->assertJsonPath('status', 200);
        $requestId = $requestResponse->json('data.id_trinh_ky');
        DB::table('trinh_ky')->where('id_trinh_ky', $requestId)->update(['trang_thai' => 'cho_cap_so']);

        $this->withSession($session)->postJson('/api/admin/trinh-ky/xu-ly', [
            'id_trinh_ky' => $requestId,
            'action' => 'issue',
            'id_loai_van_ban_ky_duyet' => $signerConfigId,
            'id_trinh_ky_cau_hinh_so' => $numberConfigId,
            'hinh_thuc_ky' => 'ky_chinh',
        ])->assertOk()->assertJsonPath('status', 200);

        $issued = DB::table('trinh_ky')->where('id_trinh_ky', $requestId)->first();
        $this->assertMatchesRegularExpression('/^\d+\/BC-ĐHSPKTVL-KIEMTHU$/u', $issued->so_van_ban);
        $this->assertSame((int) $numberConfigId, (int) $issued->id_trinh_ky_cau_hinh_so);
    }

    public function test_regular_account_cannot_view_or_change_signing_configuration(): void
    {
        $accountId = DB::table('tai_khoan')
            ->join('tai_khoan_chuc_vu', 'tai_khoan.id_tai_khoan', '=', 'tai_khoan_chuc_vu.id_tai_khoan')
            ->join('don_vi', 'tai_khoan_chuc_vu.id_don_vi', '=', 'don_vi.id_don_vi')
            ->where('don_vi.ten_don_vi', 'not like', '%Tổ chức%')
            ->where('don_vi.ten_don_vi', 'not like', '%TC-HC%')
            ->where('don_vi.ten_don_vi', 'not like', '%TCHC%')
            ->value('tai_khoan.id_tai_khoan');
        if (! $accountId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa có tài khoản ngoài Phòng Tổ chức - Hành chính.');
        }

        $session = [VLUTE::SESSION_IDTaiKhoan => $accountId];
        $this->withSession($session)->getJson('/api/admin/trinh-ky/meta')
            ->assertOk()
            ->assertJsonPath('status', 200)
            ->assertJsonPath('data.vai_tro.tchc', false)
            ->assertJsonCount(0, 'data.nguoi_ky')
            ->assertJsonCount(0, 'data.cau_hinh_so');

        $this->withSession($session)->getJson('/api/admin/trinh-ky/cau-hinh-so')
            ->assertOk()
            ->assertJsonPath('status', 400);
        $this->withSession($session)->getJson('/api/admin/trinh-ky/cau-hinh-email')
            ->assertOk()
            ->assertJsonPath('status', 400);
        $this->withSession($session)->postJson('/api/admin/trinh-ky/nguoi-ky', [
            'id_tai_khoan' => $accountId,
            'hinh_thuc_ky' => 'ky_chinh',
        ])->assertOk()->assertJsonPath('status', 400);

        $typeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if ($typeId) {
            Mail::fake();
            $requestResponse = $this->withSession($session)->postJson('/api/admin/trinh-ky', [
                'id_loai_van_ban' => $typeId,
                'id_nguoi_ky' => $accountId,
                'hinh_thuc_ky' => 'ky_chinh',
                'mo_ta' => 'Kiểm thử tài khoản thường không được cấp số',
            ])->assertOk()->assertJsonPath('status', 200);
            $requestId = $requestResponse->json('data.id_trinh_ky');
            DB::table('trinh_ky')->where('id_trinh_ky', $requestId)->update(['trang_thai' => 'cho_cap_so']);

            $this->withSession($session)->postJson('/api/admin/trinh-ky/xu-ly', [
                'id_trinh_ky' => $requestId,
                'action' => 'issue',
            ])->assertOk()->assertJsonPath('status', 400);

            $this->withSession($session)->getJson('/api/admin/trinh-ky?pham_vi=duyet_cap_so')
                ->assertOk()->assertJsonPath('status', 400);
        }
    }

    public function test_owner_and_administrative_office_can_manage_request_files(): void
    {
        $ownerId = DB::table('tai_khoan')->value('id_tai_khoan');
        $adminId = $this->administrativeOfficeAccountId();
        $typeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if (! $ownerId || ! $adminId || ! $typeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa đủ tài khoản hoặc loại văn bản để kiểm thử file.');
        }

        Mail::fake();
        $requestResponse = $this->withSession([VLUTE::SESSION_IDTaiKhoan => $ownerId])->postJson('/api/admin/trinh-ky', [
            'id_loai_van_ban' => $typeId,
            'id_nguoi_ky' => $ownerId,
            'hinh_thuc_ky' => 'ky_chinh',
            'mo_ta' => 'Kiểm thử quyền quản lý file trình ký',
        ])->assertOk()->assertJsonPath('status', 200);
        $requestId = $requestResponse->json('data.id_trinh_ky');

        $fileResponse = $this->withSession([VLUTE::SESSION_IDTaiKhoan => $ownerId])->postJson('/api/admin/trinh-ky/files', [
            'id_trinh_ky' => $requestId,
            'path_s3' => 'https://minio.example.test/dev/tests/quyen-file.docx',
            'ten_file' => 'quyen-file.docx',
            'dung_luong' => '4 B',
            'type' => 'docx',
            'loai_file' => 'du_thao',
        ])->assertOk()->assertJsonPath('status', 200);
        $fileId = $fileResponse->json('data.id_file');

        $this->withSession([VLUTE::SESSION_IDTaiKhoan => $adminId])->deleteJson('/api/admin/trinh-ky/files?id_file='.$fileId)
            ->assertOk()->assertJsonPath('status', 200);
        $this->assertDatabaseMissing('files', ['id_file' => $fileId]);
    }

    public function test_owner_can_update_requested_fields_and_existing_files_after_change_request(): void
    {
        $ownerId = DB::table('tai_khoan')->value('id_tai_khoan');
        $typeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        $changedTypeId = DB::table('loai_van_ban')->where('is_su_dung', 1)
            ->where('id_loai_van_ban', '!=', $typeId)->value('id_loai_van_ban') ?: $typeId;
        if (! $ownerId || ! $typeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa đủ tài khoản hoặc loại văn bản để kiểm thử chỉnh sửa.');
        }

        Mail::fake();
        $session = [VLUTE::SESSION_IDTaiKhoan => $ownerId];
        $requestResponse = $this->withSession($session)->postJson('/api/admin/trinh-ky', [
            'id_loai_van_ban' => $typeId,
            'id_nguoi_ky' => $ownerId,
            'hinh_thuc_ky' => 'ky_chinh',
            'mo_ta' => 'Trích yếu trước khi yêu cầu chỉnh sửa',
            'ngay_ban_hanh' => '2026-08-01',
        ])->assertOk()->assertJsonPath('status', 200);
        $requestId = $requestResponse->json('data.id_trinh_ky');
        $documentId = $requestResponse->json('data.id_van_ban');

        $fileResponse = $this->withSession($session)->postJson('/api/admin/trinh-ky/files', [
            'id_trinh_ky' => $requestId,
            'path_s3' => 'https://minio.example.test/dev/tests/file-can-chinh-sua.docx',
            'ten_file' => 'file-can-chinh-sua.docx',
            'dung_luong' => '8 KB',
            'type' => 'docx',
            'loai_file' => 'du_thao',
        ])->assertOk()->assertJsonPath('status', 200);
        $fileId = $fileResponse->json('data.id_file');
        DB::table('trinh_ky')->where('id_trinh_ky', $requestId)->update(['trang_thai' => 'can_sua']);

        $this->withSession($session)->putJson('/api/admin/trinh-ky', [
            'id_trinh_ky' => $requestId,
            'id_loai_van_ban' => $changedTypeId,
            'id_nguoi_ky' => $ownerId,
            'hinh_thuc_ky' => 'ky_thay',
            'mo_ta' => 'Trích yếu đã cập nhật theo góp ý',
            'ngay_ban_hanh' => '2026-08-07',
        ])->assertOk()->assertJsonPath('status', 200);

        $this->assertDatabaseHas('trinh_ky', [
            'id_trinh_ky' => $requestId,
            'id_loai_van_ban' => $changedTypeId,
            'mo_ta' => 'Trích yếu đã cập nhật theo góp ý',
            'ngay_ban_hanh' => '2026-08-07',
            'trang_thai' => 'cho_duyet',
        ]);
        $this->assertDatabaseHas('van_ban', [
            'id_van_ban' => $documentId,
            'id_loai_van_ban' => $changedTypeId,
            'tieu_de' => 'Trích yếu đã cập nhật theo góp ý',
        ]);
        $this->withSession($session)->getJson('/api/admin/trinh-ky/chi-tiet?id_trinh_ky='.$requestId)
            ->assertOk()->assertJsonPath('status', 200)
            ->assertJsonPath('data.files.0.id_file', $fileId);

        $this->withSession($session)->deleteJson('/api/admin/trinh-ky/files?id_file='.$fileId)
            ->assertOk()->assertJsonPath('status', 200);
        $this->assertDatabaseMissing('files', ['id_file' => $fileId]);
    }

    public function test_unit_manager_receives_approve_change_and_reject_actions(): void
    {
        $manager = DB::table('tai_khoan')
            ->join('tai_khoan_chuc_vu', 'tai_khoan.id_tai_khoan', '=', 'tai_khoan_chuc_vu.id_tai_khoan')
            ->join('chuc_vu', 'tai_khoan_chuc_vu.id_chuc_vu', '=', 'chuc_vu.id_chuc_vu')
            ->join('don_vi', 'tai_khoan_chuc_vu.id_don_vi', '=', 'don_vi.id_don_vi')
            ->where('chuc_vu.truong_don_vi', 1)
            ->where('don_vi.ten_don_vi', 'not like', '%Tổ chức%')
            ->where('don_vi.ten_don_vi', 'not like', '%TC-HC%')
            ->where('don_vi.ten_don_vi', 'not like', '%TCHC%')
            ->select('tai_khoan.id_tai_khoan', 'tai_khoan_chuc_vu.id_don_vi')
            ->first();
        $employeeId = $manager
            ? TaiKhoanChucVu::taiKhoanTheoDonVi((int) $manager->id_don_vi)->where('id_tai_khoan', '!=', $manager->id_tai_khoan)->value('id_tai_khoan')
            : null;
        $typeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if (! $manager || ! $employeeId || ! $typeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa đủ trưởng đơn vị và nhân viên cùng đơn vị.');
        }

        Mail::fake();
        $requestResponse = $this->withSession([VLUTE::SESSION_IDTaiKhoan => $employeeId])->postJson('/api/admin/trinh-ky', [
            'id_loai_van_ban' => $typeId,
            'id_nguoi_ky' => $manager->id_tai_khoan,
            'hinh_thuc_ky' => 'ky_chinh',
            'mo_ta' => 'Kiểm thử nút xử lý của trưởng đơn vị',
        ])->assertOk()->assertJsonPath('status', 200);

        $detail = $this->withSession([VLUTE::SESSION_IDTaiKhoan => $manager->id_tai_khoan])
            ->getJson('/api/admin/trinh-ky/chi-tiet?id_trinh_ky='.$requestResponse->json('data.id_trinh_ky'))
            ->assertOk()->assertJsonPath('status', 200);

        $this->assertEqualsCanonicalizing(
            ['approve', 'request_changes', 'reject'],
            $detail->json('data.quyen_thao_tac')
        );
    }

    public function test_workflow_emails_are_queued_for_the_correct_recipients(): void
    {
        $manager = DB::table('tai_khoan')
            ->join('tai_khoan_chuc_vu', 'tai_khoan.id_tai_khoan', '=', 'tai_khoan_chuc_vu.id_tai_khoan')
            ->join('chuc_vu', 'tai_khoan_chuc_vu.id_chuc_vu', '=', 'chuc_vu.id_chuc_vu')
            ->where('chuc_vu.truong_don_vi', 1)
            ->whereNotNull('tai_khoan_chuc_vu.id_don_vi')
            ->select('tai_khoan.id_tai_khoan', 'tai_khoan_chuc_vu.id_don_vi', 'tai_khoan.email')
            ->first();
        $employee = $manager ? TaiKhoanChucVu::taiKhoanTheoDonVi((int) $manager->id_don_vi)
            ->where('id_tai_khoan', '!=', $manager->id_tai_khoan)
            ->whereNotNull('email')
            ->select('id_tai_khoan', 'email')
            ->first() : null;
        $administrativeOfficeId = $this->administrativeOfficeAccountId();
        $administrativeOfficeEmail = $administrativeOfficeId
            ? DB::table('tai_khoan')->where('id_tai_khoan', $administrativeOfficeId)->value('email')
            : null;
        $typeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if (! $manager || ! $employee || ! $administrativeOfficeEmail || ! $typeId) {
            $this->markTestSkipped('Cơ sở dữ liệu chưa đủ người gửi, trưởng đơn vị và tài khoản TC-HC để kiểm thử email.');
        }

        Queue::fake();
        Mail::fake();
        $requestResponse = $this->withSession([VLUTE::SESSION_IDTaiKhoan => $employee->id_tai_khoan])
            ->postJson('/api/admin/trinh-ky', [
                'id_loai_van_ban' => $typeId,
                'id_nguoi_ky' => $manager->id_tai_khoan,
                'hinh_thuc_ky' => 'ky_chinh',
                'mo_ta' => 'Kiểm thử định tuyến email trình ký',
            ])->assertOk()->assertJsonPath('status', 200);
        $requestId = $requestResponse->json('data.id_trinh_ky');

        $createdMailJobs = Queue::pushed(sendEmail::class)
            ->filter(fn (sendEmail $job) => $job->data['su_kien'] === 'created');
        $this->assertTrue(
            $createdMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === $manager->email
                && $job->data['cap_nhat_tiep_nhan'] === false),
            'Người nhận thực tế: '.$createdMailJobs->map(fn (sendEmail $job) => $job->data['email'])->implode(', ')
        );

        $this->withSession([VLUTE::SESSION_IDTaiKhoan => $manager->id_tai_khoan])
            ->postJson('/api/admin/trinh-ky/xu-ly', [
                'id_trinh_ky' => $requestId,
                'action' => 'approve',
            ])->assertOk()->assertJsonPath('status', 200);

        $approvalMailJobs = Queue::pushed(sendEmail::class)
            ->filter(fn (sendEmail $job) => $job->data['su_kien'] === 'approve');
        $this->assertTrue(
            $approvalMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === trim($administrativeOfficeEmail)),
            'Người nhận TC-HC thực tế: '.$approvalMailJobs->map(fn (sendEmail $job) => $job->data['email'])->implode(', ')
        );
        $this->assertTrue(
            $approvalMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === trim($employee->email)),
            'Người tạo yêu cầu chưa nhận thông báo đã duyệt.'
        );

        $this->withSession([VLUTE::SESSION_IDTaiKhoan => $administrativeOfficeId])
            ->postJson('/api/admin/trinh-ky/xu-ly', [
                'id_trinh_ky' => $requestId,
                'action' => 'issue',
                'id_nguoi_ky' => $administrativeOfficeId,
                'hinh_thuc_ky' => 'ky_chinh',
            ])->assertOk()->assertJsonPath('status', 200);
        $issueMailJobs = Queue::pushed(sendEmail::class)
            ->filter(fn (sendEmail $job) => $job->data['su_kien'] === 'issue');
        $this->assertTrue(
            $issueMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === trim($employee->email)),
            'Người tạo yêu cầu chưa nhận thông báo cấp số.'
        );
        $this->assertTrue(
            $issueMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === trim($manager->email)),
            'Trưởng/Phó đơn vị chưa nhận thông báo cấp số.'
        );
        $this->assertFalse(
            $issueMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === trim($administrativeOfficeEmail)),
            'Người ký/TC-HC không thuộc danh sách nhận thông báo cấp số.'
        );

        DB::table('trinh_ky')->where('id_trinh_ky', $requestId)->update(['trang_thai' => 'cho_duyet']);
        $this->withSession([VLUTE::SESSION_IDTaiKhoan => $manager->id_tai_khoan])
            ->postJson('/api/admin/trinh-ky/xu-ly', [
                'id_trinh_ky' => $requestId,
                'action' => 'request_changes',
                'ghi_chu' => 'Vui lòng cập nhật lại file và trích yếu.',
            ])->assertOk()->assertJsonPath('status', 200);

        $changeMailJobs = Queue::pushed(sendEmail::class)
            ->filter(fn (sendEmail $job) => $job->data['su_kien'] === 'request_changes');
        $this->assertTrue(
            $changeMailJobs->contains(fn (sendEmail $job) => $job->data['email'] === $employee->email),
            'Người nhận chỉnh sửa thực tế: '.$changeMailJobs->map(fn (sendEmail $job) => $job->data['email'])->implode(', ')
        );
    }

    public function test_administrative_office_can_configure_email_events_for_each_account(): void
    {
        $administrativeOfficeId = $this->administrativeOfficeAccountId();
        if (! $administrativeOfficeId) {
            $this->markTestSkipped('Chưa có tài khoản Phòng TC-HC để kiểm thử cấu hình email.');
        }

        $session = [VLUTE::SESSION_IDTaiKhoan => $administrativeOfficeId];
        $response = $this->withSession($session)->getJson('/api/admin/trinh-ky/cau-hinh-email')
            ->assertOk()->assertJsonPath('status', 200)
            ->assertJsonStructure(['data' => ['truong_don_vi', 'tchc', 'su_kien_truong_don_vi', 'su_kien_tchc']]);
        $manager = collect($response->json('data.truong_don_vi'))->first();
        $administrativeAccount = collect($response->json('data.tchc'))->first();
        if (! $manager || ! $administrativeAccount) {
            $this->markTestSkipped('Chưa đủ danh sách Trưởng/Phó đơn vị hoặc tài khoản TC-HC.');
        }

        $this->withSession($session)->postJson('/api/admin/trinh-ky/cau-hinh-email', [
            'cau_hinh' => [
                ['id_tai_khoan' => $manager['id_tai_khoan'], 'su_kien' => 'created', 'is_nhan' => false],
                ['id_tai_khoan' => $manager['id_tai_khoan'], 'su_kien' => 'updated', 'is_nhan' => true],
                ['id_tai_khoan' => $manager['id_tai_khoan'], 'su_kien' => 'issue', 'is_nhan' => true],
                ['id_tai_khoan' => $administrativeAccount['id_tai_khoan'], 'su_kien' => 'approve', 'is_nhan' => true],
            ],
        ])->assertOk()->assertJsonPath('status', 200);

        $this->assertDatabaseHas('trinh_ky_cau_hinh_email', [
            'id_tai_khoan' => $manager['id_tai_khoan'],
            'su_kien' => 'created',
            'is_nhan' => 0,
        ]);
        $this->assertDatabaseHas('trinh_ky_cau_hinh_email', [
            'id_tai_khoan' => $administrativeAccount['id_tai_khoan'],
            'su_kien' => 'approve',
            'is_nhan' => 1,
        ]);

        $employeeId = TaiKhoanChucVu::taiKhoanTheoDonVi((int) $manager['id_don_vi'])
            ->where('id_tai_khoan', '!=', $manager['id_tai_khoan'])->value('id_tai_khoan');
        $typeId = DB::table('loai_van_ban')->where('is_su_dung', 1)->value('id_loai_van_ban');
        if ($employeeId && $typeId) {
            Queue::fake();
            Mail::fake();
            $this->withSession([VLUTE::SESSION_IDTaiKhoan => $employeeId])->postJson('/api/admin/trinh-ky', [
                'id_loai_van_ban' => $typeId,
                'id_nguoi_ky' => $manager['id_tai_khoan'],
                'hinh_thuc_ky' => 'ky_chinh',
                'mo_ta' => 'Kiểm thử tắt thông báo yêu cầu mới',
            ])->assertOk()->assertJsonPath('status', 200);
            Queue::assertNotPushed(sendEmail::class, fn (sendEmail $job) => $job->data['su_kien'] === 'created'
                && $job->data['email'] === trim((string) $manager['email']));
        }
    }

    private function administrativeOfficeAccountId(): ?int
    {
        $id = DB::table('tai_khoan')
            ->join('tai_khoan_chuc_vu', 'tai_khoan.id_tai_khoan', '=', 'tai_khoan_chuc_vu.id_tai_khoan')
            ->join('don_vi', 'tai_khoan_chuc_vu.id_don_vi', '=', 'don_vi.id_don_vi')
            ->where(function ($query) {
                $query->where(function ($unit) {
                    $unit->where('don_vi.ten_don_vi', 'like', '%Tổ chức%')
                        ->where('don_vi.ten_don_vi', 'like', '%Hành chính%');
                })->orWhere('don_vi.ten_don_vi', 'like', '%TC-HC%')
                    ->orWhere('don_vi.ten_don_vi', 'like', '%TCHC%');
            })
            ->value('tai_khoan.id_tai_khoan');

        return $id ? (int) $id : null;
    }
}
