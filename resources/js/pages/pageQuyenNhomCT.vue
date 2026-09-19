<template>
    <AppContent>
        <br>
        <AppCard :title="`Nhóm quyền / ${tieuDe}`">
            <template #tools>
                <RouterLink :to="{ name: 'router-quyennhom' }" class="btn btn-sm btn-default">
                    <i class="bi bi-arrow-left"></i><span>Danh sách</span>
                </RouterLink>
            </template>

            <div v-if="loi" class="alert alert-danger py-2">{{ loi }}</div>

            <AppTable
                :items="tableChiTiet.list"
                :columns="columnsChiTiet"
                item-key="id_quyen_nhom_chi_tiet"
                group-by="ten_nhom"
                min-width="720px"
                :show-pagination="false"
                empty-text="Nhóm này chưa được gán quyền nào."
            >
                <template #toolbar>
                    <div class="app-toolbar-actions">
                        <button type="button" class="btn btn-primary" @click="moChonQuyen">
                            <i class="bi bi-plus-lg"></i><span>Thêm quyền</span>
                        </button>
                    </div>

                    <div class="input-group app-toolbar-search">
                        <input
                            v-model="boLocQuyen.s"
                            type="text"
                            class="form-control"
                            placeholder="Tìm theo tiêu đề quyền và nhấn Enter ..."
                            @keyup.enter="getQuyenNhomCT"
                        >
                        <button type="button" class="btn btn-default" title="Tìm kiếm" @click="getQuyenNhomCT">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </template>

                <template #group-header="{ groupValue }">
                    <strong>{{ groupValue }}</strong>
                    <span class="badge text-bg-secondary ms-1">{{ demTheoNhom[groupValue] }}</span>
                </template>

                <template #actions="{ item }">
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Gỡ khỏi nhóm" @click="xoaChiTiet(item)">
                        <i class="bi bi-trash3"></i>
                    </button>
                </template>
            </AppTable>
        </AppCard>

        <AppCard title="Tài khoản của nhóm">
            <AppTable
                :items="tableTaiKhoan.list"
                :columns="columnsTaiKhoan"
                :pagination="tableTaiKhoan.pagination"
                item-key="id_quyen_nhom_tai_khoan"
                min-width="720px"
                empty-text="Nhóm này chưa có tài khoản nào."
                @page-change="getQuyenNhomTK"
            >
                <template #toolbar>
                    <div class="app-toolbar-actions">
                        <button type="button" class="btn btn-primary" @click="moChonTaiKhoan">
                            <i class="bi bi-person-plus"></i><span>Thêm tài khoản</span>
                        </button>
                    </div>

                    <div class="input-group app-toolbar-search">
                        <input
                            v-model="boLocTaiKhoan.s"
                            type="text"
                            class="form-control"
                            placeholder="Tìm theo họ tên hoặc email và nhấn Enter ..."
                            @keyup.enter="getQuyenNhomTK(1)"
                        >
                        <button type="button" class="btn btn-default" title="Tìm kiếm" @click="getQuyenNhomTK(1)">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </template>

                <template #cell-trang_thai="{ value }">
                    <span class="badge" :class="value ? 'text-bg-success' : 'text-bg-secondary'">
                        {{ value ? 'Hoạt động' : 'Khóa' }}
                    </span>
                </template>

                <template #actions="{ item }">
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Gỡ khỏi nhóm" @click="xoaTaiKhoan(item)">
                        <i class="bi bi-trash3"></i>
                    </button>
                </template>
            </AppTable>
        </AppCard>

        <AppModal
            ref="modalChonQuyen"
            title="Chọn quyền cho nhóm"
            icon="bi-list-check"
            size="xl"
            :show-ok="false"
            cancel-label="Đóng"
            @hidden="getQuyenNhomCT"
        >
            <div v-if="loiChon" class="alert alert-danger py-2">{{ loiChon }}</div>

            <AppTable
                :items="tableChonQuyen.list"
                :columns="columnsChonQuyen"
                :pagination="tableChonQuyen.pagination"
                item-key="id_quyen_chi_tiet"
                group-by="ten_nhom"
                min-width="640px"
                small
                empty-text="Không có quyền chi tiết nào."
                @page-change="getDsQuyenChiTiet"
            >
                <template #toolbar>
                    <div class="input-group app-toolbar-search">
                        <input
                            v-model="boLocChonQuyen.s"
                            type="text"
                            class="form-control"
                            placeholder="Tìm theo nhóm quyền, tiêu đề hoặc funcs rồi nhấn Enter ..."
                            @keyup.enter="getDsQuyenChiTiet(1)"
                        >
                        <button type="button" class="btn btn-default" title="Tìm kiếm" @click="getDsQuyenChiTiet(1)">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </template>

                <template #actions="{ item }">
                    <span v-if="item.da_chon" class="text-success" title="Đã có trong nhóm">
                        <i class="bi bi-check-lg"></i>
                    </span>
                    <button v-else type="button" class="btn btn-sm btn-outline-primary" title="Thêm vào nhóm" @click="themQuyen(item)">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </template>
            </AppTable>
        </AppModal>

        <AppModal
            ref="modalChonTaiKhoan"
            title="Chọn tài khoản cho nhóm"
            icon="bi-person-plus"
            size="lg"
            :show-ok="false"
            cancel-label="Đóng"
            @hidden="getQuyenNhomTK(1)"
        >
            <div v-if="loiChonTK" class="alert alert-danger py-2">{{ loiChonTK }}</div>

            <AppTable
                :items="tableChonTaiKhoan.list"
                :columns="columnsChonTaiKhoan"
                :pagination="tableChonTaiKhoan.pagination"
                item-key="id_tai_khoan"
                min-width="560px"
                small
                empty-text="Không có tài khoản nào."
                @page-change="getDsTaiKhoanChon"
            >
                <template #toolbar>
                    <div class="input-group app-toolbar-search">
                        <input
                            v-model="boLocChonTaiKhoan.s"
                            type="text"
                            class="form-control"
                            placeholder="Tìm theo họ tên hoặc email rồi nhấn Enter ..."
                            @keyup.enter="getDsTaiKhoanChon(1)"
                        >
                        <button type="button" class="btn btn-default" title="Tìm kiếm" @click="getDsTaiKhoanChon(1)">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </template>

                <template #actions="{ item }">
                    <span v-if="item.da_chon" class="text-success" title="Đã có trong nhóm">
                        <i class="bi bi-check-lg"></i>
                    </span>
                    <button v-else type="button" class="btn btn-sm btn-outline-primary" title="Thêm vào nhóm" @click="themTaiKhoan(item)">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </template>
            </AppTable>
        </AppModal>

        <AppConfirm ref="xacNhan" />
    </AppContent>
</template>

<script>
import { RouterLink } from 'vue-router'

export default {
    name: 'pageQuyenNhomCT',
    components: { RouterLink },
    data() {
        return {
            loi: '',
            loiChon: '',
            loiChonTK: '',
            tieuDe: '',
            boLocQuyen: { s: '' },
            boLocTaiKhoan: { s: '' },
            boLocChonQuyen: { s: '' },
            boLocChonTaiKhoan: { s: '' },
            tableChiTiet: { list: [] },
            tableTaiKhoan: { pagination: {}, list: [] },
            tableChonQuyen: { pagination: {}, list: [] },
            tableChonTaiKhoan: { pagination: {}, list: [] },
            columnsChiTiet: [
                { key: 'tieu_de', label: 'Tiêu đề' },
                { key: 'funcs', label: 'Funcs (Controller.Func)' },
                { key: 'show_views', label: 'Show views' },
            ],
            columnsTaiKhoan: [
                { key: 'ho_ten', label: 'Họ tên' },
                { key: 'email', label: 'Email' },
                { key: 'trang_thai', label: 'Trạng thái' },
            ],
            columnsChonQuyen: [
                { key: 'tieu_de', label: 'Tiêu đề' },
                { key: 'funcs', label: 'Funcs (Controller.Func)' },
            ],
            columnsChonTaiKhoan: [
                { key: 'ho_ten', label: 'Họ tên' },
                { key: 'email', label: 'Email' },
            ],
        }
    },
    computed: {
        idQuyenNhom() {
            return this.$route.params.id
        },
        demTheoNhom() {
            return this.tableChiTiet.list.reduce((tong, dong) => {
                tong[dong.ten_nhom] = (tong[dong.ten_nhom] ?? 0) + 1

                return tong
            }, {})
        },
    },
    mounted() {
        this.getQuyenNhomCT()
        this.getQuyenNhomTK()
    },
    methods: {
        getQuyenNhomCT() {
            this.$axios.get(this.route('QuyenNhomController.getQuyenNhomCT', { id_quyen_nhom: this.idQuyenNhom }, false), {
                params: { s: this.boLocQuyen.s },
            }).then((response) => {
                this.tieuDe = response.data.data.tieu_de
                this.tableChiTiet.list = response.data.data.danh_sach
                this.loi = ''
            })
        },
        getQuyenNhomTK(page = 1) {
            this.$axios.get(this.route('QuyenNhomController.getQuyenNhomTK', { id_quyen_nhom: this.idQuyenNhom }, false), {
                params: { s: this.boLocTaiKhoan.s, page },
            }).then((response) => {
                this.tableTaiKhoan.list = response.data.data.data
                this.tableTaiKhoan.pagination = response.data.data
            })
        },
        moChonQuyen() {
            this.loiChon = ''
            this.boLocChonQuyen.s = ''
            this.getDsQuyenChiTiet()
            this.$refs.modalChonQuyen.open()
        },
        getDsQuyenChiTiet(page = 1) {
            this.$axios.get(this.route('QuyenNhomController.getDsQuyenChiTiet', { id_quyen_nhom: this.idQuyenNhom }, false), {
                params: { s: this.boLocChonQuyen.s, page },
            }).then((response) => {
                this.tableChonQuyen.list = response.data.data.data
                this.tableChonQuyen.pagination = response.data.data
            })
        },
        themQuyen(item) {
            this.$axios.post(this.route('QuyenNhomController.putQuyenNhomCT', { id_quyen_nhom: this.idQuyenNhom }, false), {
                id_quyen_chi_tiet: item.id_quyen_chi_tiet,
            }).then(() => {
                this.getDsQuyenChiTiet(this.tableChonQuyen.pagination.current_page)
            }).catch((e) => {
                this.loiChon = this.thongBaoLoi(e)
            })
        },
        async xoaChiTiet(item) {
            const dongY = await this.$refs.xacNhan.open({
                title: 'Gỡ quyền khỏi nhóm?',
                message: item.tieu_de,
                variant: 'danger',
                okLabel: 'Gỡ',
            })

            if (! dongY) {
                return
            }

            this.$axios.delete(this.route('QuyenNhomController.deleteQuyenNhomCT', { id_quyen_nhom: this.idQuyenNhom, id_quyen_nhom_chi_tiet: item.id_quyen_nhom_chi_tiet }, false))
                .then(() => {
                    this.getQuyenNhomCT()
                })
                .catch((e) => {
                    this.loi = this.thongBaoLoi(e)
                })
        },
        moChonTaiKhoan() {
            this.loiChonTK = ''
            this.boLocChonTaiKhoan.s = ''
            this.getDsTaiKhoanChon()
            this.$refs.modalChonTaiKhoan.open()
        },
        getDsTaiKhoanChon(page = 1) {
            this.$axios.get(this.route('QuyenNhomController.getDsTaiKhoanChon', { id_quyen_nhom: this.idQuyenNhom }, false), {
                params: { s: this.boLocChonTaiKhoan.s, page },
            }).then((response) => {
                this.tableChonTaiKhoan.list = response.data.data.data
                this.tableChonTaiKhoan.pagination = response.data.data
            })
        },
        themTaiKhoan(item) {
            this.$axios.post(this.route('QuyenNhomController.putQuyenNhomTK', { id_quyen_nhom: this.idQuyenNhom }, false), {
                id_tai_khoan: item.id_tai_khoan,
            }).then(() => {
                this.getDsTaiKhoanChon(this.tableChonTaiKhoan.pagination.current_page)
            }).catch((e) => {
                this.loiChonTK = this.thongBaoLoi(e)
            })
        },
        async xoaTaiKhoan(item) {
            const dongY = await this.$refs.xacNhan.open({
                title: 'Gỡ tài khoản khỏi nhóm?',
                message: item.ho_ten,
                variant: 'danger',
                okLabel: 'Gỡ',
            })

            if (! dongY) {
                return
            }

            this.$axios.delete(this.route('QuyenNhomController.deleteQuyenNhomTK', { id_quyen_nhom: this.idQuyenNhom, id_quyen_nhom_tai_khoan: item.id_quyen_nhom_tai_khoan }, false))
                .then(() => {
                    this.getQuyenNhomTK(this.tableTaiKhoan.pagination.current_page)
                })
                .catch((e) => {
                    this.loi = this.thongBaoLoi(e)
                })
        },
        thongBaoLoi(e) {
            return e.response?.data?.message || 'Không thực hiện được, vui lòng thử lại.'
        },
    },
}
</script>

<style scoped>
:deep(.app-col-tieu_de) {
    width: 260px;
}

:deep(.app-col-funcs) {
    width: auto;
    min-width: 220px;
}

:deep(.app-col-funcs),
:deep(.app-col-show_views) {
    font-family: var(--bs-font-monospace);
    font-size: 0.8125rem;
    word-break: break-word;
}

:deep(.app-col-show_views) {
    width: 160px;
}

:deep(.app-col-ho_ten) {
    width: 260px;
}

:deep(.app-col-trang_thai) {
    width: 130px;
    text-align: center;
}
</style>
