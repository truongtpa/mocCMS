<template>
    <LteAppContent title="Bài viết">
        <LteCard title="Danh sách bài viết">
            <AppTable
                :items="tableBaiViet.list"
                :columns="columns"
                :pagination="tableBaiViet.pagination"
                :loading="dangTai"
                :highlight="boLoc.daApDung"
                item-key="id_tin_tuc"
                empty-text="Không tìm thấy bài viết nào."
                selectable
                show-index
                sticky-header
                v-model:sort-key="boLoc.sortKey"
                v-model:sort-order="boLoc.sortOrder"
                @sort-change="getBaiViet(1)"
                @page-change="getBaiViet"
                @selection-change="daChon = $event"
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
                                <button type="button" class="btn btn-secondary" title="Xóa bộ lọc" @click="xoaBoLoc">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <template #selection-actions>
                    <button type="button" class="btn btn-sm btn-outline-danger" @click="xoaNhieu">
                        <i class="bi bi-trash3"></i> Xóa mục đã chọn
                    </button>
                </template>

                <template #cell-thumbnail="{ value, item }">
                    <img
                        v-if="value"
                        :src="value"
                        :alt="item.tieu_de"
                        class="rounded app-thumb"
                        loading="lazy"
                    >
                    <span v-else class="text-muted">—</span>
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
        </LteCard>

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
    </LteAppContent>
</template>

<script>
export default {
    name: 'pageBaiViet',
    data() {
        return {
            dangTai: false,
            daChon: [],
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
            columns: [
                { key: 'thumbnail', label: 'Ảnh', width: '90px', align: 'center' },
                { key: 'tieu_de', label: 'Tiêu đề', width: '26%', sortable: true },
                {
                    key: 'noi_dung',
                    label: 'Nội dung',
                    formatter: (v) => this.tomTat(v),
                },
                { key: 'xuat_ban', label: 'Trạng thái', align: 'center', nowrap: true, sortable: true },
                {
                    key: 'ngay_tao',
                    label: 'Ngày tạo',
                    nowrap: true,
                    sortable: true,
                    formatter: (v) => this.formatNgay(v),
                },
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
        xoaBoLoc() {
            this.boLoc.s = ''
            this.getBaiViet(1)
        },
        getBaiViet(page = 1) {
            this.dangTai = true

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
            }).finally(() => {
                this.dangTai = false
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
        async xoaNhieu() {
            const dongY = await this.$refs.xacNhan.open({
                title: `Xóa ${this.daChon.length} bài viết?`,
                message: 'Các bài viết đã chọn sẽ bị xóa khỏi hệ thống.',
                detail: 'Hành động này không thể hoàn tác.',
                variant: 'danger',
                okLabel: 'Xóa tất cả',
            })

            if (dongY) {
                // TODO: gọi API xóa hàng loạt khi endpoint sẵn sàng
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
.app-thumb {
    width: 72px;
    height: 48px;
    object-fit: cover;
}

.app-noi-dung {
    white-space: pre-line;
    line-height: 1.7;
    max-height: 45vh;
    overflow-y: auto;
}
</style>
