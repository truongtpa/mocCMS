<template>
    <AppContent>
        <br>
        <AppCard title="Bài viết / Danh sách">
            <AppTable
                :items="tableBaiViet.list"
                :columns="columns"
                :pagination="tableBaiViet.pagination"
                :highlight="boLoc.daApDung"
                item-key="id_tin_tuc"
                empty-text="Không tìm thấy bài viết nào."
                sticky-header
                v-model:sort-key="boLoc.sortKey"
                v-model:sort-order="boLoc.sortOrder"
                @sort-change="getBaiViet(1)"
                @page-change="getBaiViet"
            >
                <template #toolbar>
                    <div class="row g-2">
                        <div class="col-lg-5 col-12">
                            <div class="input-group">
                                <input
                                    v-model="boLoc.s"
                                    type="text"
                                    class="form-control"
                                    placeholder="Nhập tiêu đề bài viết và nhấn Enter ..."
                                    @keyup.enter="timKiem"
                                >
                                <button type="button" class="btn btn-primary" title="Tìm kiếm" @click="timKiem">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <template #cell-thumbnail="{ value, item }">
                    <AppImage :src="value" :alt="item.tieu_de" width="72" height="48" class="rounded" />
                </template>

                <template #cell-tieu_de="{ item, highlight }">
                    <div class="app-bv-tieu-de">
                        <template v-for="(phan, i) in highlight(item.tieu_de)" :key="i"><mark
                            v-if="phan.hit"
                            class="app-bv-mark"
                        >{{ phan.text }}</mark><template v-else>{{ phan.text }}</template></template>
                    </div>
                    <div class="app-bv-tom-tat">{{ tomTat(item.noi_dung, 220) }}</div>
                </template>

                <template #cell-xuat_ban="{ value }">
                    <span class="badge" :class="value ? 'text-bg-success' : 'text-bg-secondary'">
                        {{ value ? 'Đã xuất bản' : 'Bản nháp' }}
                    </span>
                </template>

                <template #actions="{ item }">
                    <button type="button" class="btn btn-sm btn-outline-primary" title="Xem chi tiết" @click="xemChiTiet(item)">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa" @click="xoa(item)">
                        <i class="bi bi-trash3"></i>
                    </button>
                </template>
            </AppTable>
        </AppCard>

        <!-- Modal xem chi tiết -->
        <AppModal
            ref="modalChiTiet"
            :title="chiTiet.tieu_de || 'Chi tiết bài viết'"
            icon="bi-newspaper"
            size="xl"
            :show-ok="false"
            cancel-label="Đóng"
        >
            <div class="row g-3">
                <div v-if="chiTiet.thumbnail" class="col-md-4">
                    <img :src="chiTiet.thumbnail" :alt="chiTiet.tieu_de" class="img-fluid rounded">
                </div>
                <div :class="chiTiet.thumbnail ? 'col-md-8' : 'col-12'">
                    <dl class="row mb-0 small">
                        <dt class="col-4">Trạng thái</dt>
                        <dd class="col-8">
                            <span class="badge" :class="chiTiet.xuat_ban ? 'text-bg-success' : 'text-bg-secondary'">
                                {{ chiTiet.xuat_ban ? 'Đã xuất bản' : 'Bản nháp' }}
                            </span>
                        </dd>
                        <dt class="col-4">Ngày tạo</dt>
                        <dd class="col-8">{{ formatNgay(chiTiet.ngay_tao) }}</dd>
                        <dt class="col-4">Đường dẫn</dt>
                        <dd class="col-8 text-break">{{ chiTiet.url || '—' }}</dd>
                    </dl>
                </div>
                <div class="col-12">
                    <hr>
                    <div class="app-noi-dung">{{ tomTat(chiTiet.noi_dung, 2000) }}</div>
                </div>
            </div>
        </AppModal>

        <AppConfirm ref="xacNhan" />
    </AppContent>
</template>

<script>
export default {
    name: 'pageBaiViet',
    data() {
        return {
            chiTiet: {},
            boLoc: {
                s: '',
                daApDung: '',
                sortKey: 'ngay_tao',
                sortOrder: 'desc',
            },
            tableBaiViet: {
                pagination: {},
                list: [],
            },
            // Chỉ dữ liệu + hành vi. Bề rộng/căn lề xem khối <style> cuối file.
            columns: [
                { key: 'thumbnail', label: 'Ảnh' },
                // Tiêu đề + tóm tắt nội dung gộp chung một ô, xem slot #cell-tieu_de
                { key: 'tieu_de', label: 'Tiêu đề', sortable: true },
                { key: 'xuat_ban', label: 'Trạng thái', sortable: true },
                { key: 'ngay_tao', label: 'Ngày tạo', sortable: true, formatter: (v) => this.formatNgay(v) },
            ],
        }
    },
    mounted() {
        this.getBaiViet()
    },
    methods: {
        timKiem() {
            this.getBaiViet(1)
        },
        getBaiViet(page = 1) {
            this.$axios.get(this.route('BaiVietController.getBaiViet', undefined, false), {
                params: {
                    s: this.boLoc.s,
                    sort: this.boLoc.sortKey,
                    order: this.boLoc.sortOrder,
                    page,
                },
            }).then((response) => {
                this.tableBaiViet.list = response.data.data.data
                this.tableBaiViet.pagination = response.data.data
                this.boLoc.daApDung = this.boLoc.s
            })
        },
        xemChiTiet(item) {
            this.chiTiet = item
            this.$refs.modalChiTiet.open()
        },
        async xoa(item) {
            const dongY = await this.$refs.xacNhan.open({
                title: 'Xóa bài viết?',
                message: item.tieu_de,
                detail: 'Hành động này không thể hoàn tác.',
                variant: 'danger',
                okLabel: 'Xóa',
            })

            if (dongY) {
                // TODO: gọi API xóa khi endpoint sẵn sàng
            }
        },
        tomTat(noiDung, gioiHan = 180) {
            const chuoi = String(noiDung ?? '')
                .replace(/<[^>]*>/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()

            return chuoi.length > gioiHan ? `${chuoi.slice(0, gioiHan)}…` : chuoi
        },
        formatNgay(ngayTao) {
            if (!ngayTao) {
                return ''
            }

            return new Intl.DateTimeFormat('vi-VN', {
                dateStyle: 'short',
                timeStyle: 'short',
            }).format(new Date(ngayTao))
        },
    },
}
</script>

<style scoped>
/* Trình bày từng cột — AppTable gắn sẵn class app-col-<key> lên cả th và td */
:deep(.app-col-thumbnail) {
    width: 90px;
    text-align: center;
}

/* Cột gộp lấy hết phần còn lại */
:deep(.app-col-tieu_de) {
    width: auto;
    min-width: 260px;
}

.app-bv-tieu-de {
    font-weight: 600;
    color: var(--app-text);
}

.app-bv-tom-tat {
    margin-top: 0.15rem;
    font-size: 0.8125rem;
    line-height: 1.45;
    color: var(--app-text-muted);
    /* Giữ ô gọn: tóm tắt tối đa 2 dòng rồi cắt bằng dấu ba chấm */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.app-bv-mark {
    padding: 0 0.1em;
    background: var(--bs-warning-bg-subtle, #fff3cd);
    color: inherit;
    border-radius: 2px;
}

:deep(.app-col-xuat_ban) {
    width: 120px;
    text-align: center;
    white-space: nowrap;
}

:deep(.app-col-ngay_tao) {
    width: 130px;
    white-space: nowrap;
}

.app-noi-dung {
    white-space: pre-line;
    line-height: 1.7;
    max-height: 45vh;
    overflow-y: auto;
}
</style>
