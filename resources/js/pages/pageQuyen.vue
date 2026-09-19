<template>
    <AppContent>
        <br>
        <AppCard title="Nhóm quyền / Danh sách">
            <div v-if="loi" class="alert alert-danger py-2">{{ loi }}</div>

            <AppTable
                :items="tableQuyen.list"
                :columns="columns"
                :pagination="tableQuyen.pagination"
                :highlight="boLoc.daApDung"
                item-key="id_quyen"
                empty-text="Chưa có nhóm quyền nào."
                @page-change="getQuyen"
            >
                <template #toolbar>
                    <div class="app-toolbar-actions">
                        <button type="button" class="btn btn-primary" @click="themMoi">
                            <i class="bi bi-plus-lg"></i><span>Thêm mới</span>
                        </button>
                    </div>

                    <div class="input-group app-toolbar-search">
                        <input
                            v-model="boLoc.s"
                            type="text"
                            class="form-control"
                            placeholder="Nhập tên nhóm quyền và nhấn Enter ..."
                            @keyup.enter="timKiem"
                        >
                        <button type="button" class="btn btn-default" title="Tìm kiếm" @click="timKiem">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </template>

                <template #cell-ten_nhom="{ item, highlight }">
                    <button type="button" class="btn btn-link p-0 text-start" @click="moChiTiet(item)">
                        <template v-for="(phan, i) in highlight(item.ten_nhom)" :key="i"><mark
                            v-if="phan.hit"
                            class="app-mark"
                        >{{ phan.text }}</mark><template v-else>{{ phan.text }}</template></template>
                    </button>
                </template>

                <template #cell-so_chi_tiet="{ value }">
                    <span class="badge text-bg-secondary">{{ value }}</span>
                </template>

                <template #actions="{ item }">
                    <button type="button" class="btn btn-sm btn-outline-secondary" title="Quyền chi tiết" @click="moChiTiet(item)">
                        <i class="bi bi-list-check"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" title="Sửa" @click="chinhSua(item)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa" @click="xoa(item)">
                        <i class="bi bi-trash3"></i>
                    </button>
                </template>
            </AppTable>
        </AppCard>

        <AppModal
            ref="modalForm"
            :title="quyenForm.id_quyen ? 'Cập nhật nhóm quyền' : 'Thêm nhóm quyền'"
            icon="bi-shield-lock"
            :ok-label="quyenForm.id_quyen ? 'Cập nhật' : 'Lưu thông tin'"
            :auto-close="false"
            @ok="luuQuyen"
        >
            <div v-if="loiForm" class="alert alert-danger py-2">{{ loiForm }}</div>

            <label class="form-label" for="ten-nhom">Tên nhóm quyền</label>
            <input
                id="ten-nhom"
                v-model="quyenForm.ten_nhom"
                type="text"
                class="form-control"
                placeholder="Nhập tên nhóm quyền"
                @keyup.enter="luuQuyen"
            >
        </AppModal>

        <AppModal
            ref="modalChiTiet"
            :title="`Quyền chi tiết / ${nhomDangXem.ten_nhom}`"
            icon="bi-list-check"
            size="xl"
            :show-ok="false"
            cancel-label="Đóng"
            @hidden="getQuyen(tableQuyen.pagination.current_page)"
        >
            <div v-if="loiCT" class="alert alert-danger py-2">{{ loiCT }}</div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="ct-tieu-de">Tiêu đề</label>
                    <input id="ct-tieu-de" v-model="quyenCT.tieu_de" type="text" class="form-control" placeholder="Nhập tiêu đề">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="ct-funcs">Funcs (Controller.Func)</label>
                    <textarea id="ct-funcs" v-model="quyenCT.funcs" class="form-control font-monospace" rows="4" placeholder="Nhập funcs"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="ct-show-views">Show views</label>
                    <textarea id="ct-show-views" v-model="quyenCT.show_views" class="form-control font-monospace" rows="4" placeholder="Nhập show views"></textarea>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary" @click="luuQuyenCT">
                        <i class="bi bi-save"></i>
                        {{ quyenCT.id_quyen_chi_tiet ? 'Cập nhật' : 'Thêm mới' }}
                    </button>
                    <button v-if="quyenCT.id_quyen_chi_tiet" type="button" class="btn btn-outline-secondary ms-2" @click="huySuaCT">
                        Hủy
                    </button>
                </div>
            </div>

            <hr>

            <AppTable
                :items="tableQuyenCT.list"
                :columns="columnsCT"
                :pagination="tableQuyenCT.pagination"
                item-key="id_quyen_chi_tiet"
                min-width="640px"
                small
                empty-text="Nhóm quyền này chưa có quyền chi tiết nào."
                @page-change="getQuyenCT"
            >
                <template #actions="{ item }">
                    <button type="button" class="btn btn-sm btn-outline-primary" title="Sửa" @click="chinhSuaCT(item)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa" @click="xoaCT(item)">
                        <i class="bi bi-trash3"></i>
                    </button>
                </template>
            </AppTable>
        </AppModal>

        <AppConfirm ref="xacNhan" />
    </AppContent>
</template>

<script>
export default {
    name: 'pageQuyen',
    data() {
        return {
            loi: '',
            loiForm: '',
            loiCT: '',
            boLoc: {
                s: '',
                daApDung: '',
            },
            quyenForm: {
                id_quyen: '',
                ten_nhom: '',
            },
            nhomDangXem: {
                id_quyen: '',
                ten_nhom: '',
            },
            quyenCT: {
                id_quyen_chi_tiet: '',
                tieu_de: '',
                funcs: '',
                show_views: '',
            },
            tableQuyen: {
                pagination: {},
                list: [],
            },
            tableQuyenCT: {
                pagination: {},
                list: [],
            },
            columns: [
                { key: 'ten_nhom', label: 'Nhóm quyền' },
                { key: 'so_chi_tiet', label: 'Số quyền' },
                { key: 'ngay_cap_nhat', label: 'Ngày cập nhật', formatter: (v) => this.formatNgay(v) },
            ],
            columnsCT: [
                { key: 'tieu_de', label: 'Tiêu đề' },
                { key: 'funcs', label: 'Funcs' },
                { key: 'show_views', label: 'Show views' },
            ],
        }
    },
    mounted() {
        this.getQuyen()
    },
    methods: {
        timKiem() {
            this.getQuyen(1)
        },
        getQuyen(page = 1) {
            this.$axios.get(this.route('QuyenController.getQuyen', undefined, false), {
                params: { s: this.boLoc.s, page },
            }).then((response) => {
                this.tableQuyen.list = response.data.data.data
                this.tableQuyen.pagination = response.data.data
                this.boLoc.daApDung = this.boLoc.s
                this.loi = ''
            })
        },
        themMoi() {
            this.quyenForm = { id_quyen: '', ten_nhom: '' }
            this.loiForm = ''
            this.$refs.modalForm.open()
        },
        chinhSua(item) {
            this.quyenForm = { id_quyen: item.id_quyen, ten_nhom: item.ten_nhom }
            this.loiForm = ''
            this.$refs.modalForm.open()
        },
        luuQuyen() {
            const goi = this.quyenForm.id_quyen
                ? this.$axios.put(this.route('QuyenController.updateQuyen', { id_quyen: this.quyenForm.id_quyen }, false), { ten_nhom: this.quyenForm.ten_nhom })
                : this.$axios.post(this.route('QuyenController.putQuyen', undefined, false), { ten_nhom: this.quyenForm.ten_nhom })

            this.$refs.modalForm.setBusy(true)
            goi.then(() => {
                this.$refs.modalForm.close(true)
                this.getQuyen(this.tableQuyen.pagination.current_page)
            }).catch((e) => {
                this.loiForm = this.thongBaoLoi(e)
            }).finally(() => {
                this.$refs.modalForm.setBusy(false)
            })
        },
        async xoa(item) {
            const dongY = await this.$refs.xacNhan.open({
                title: 'Xóa nhóm quyền?',
                message: item.ten_nhom,
                detail: 'Toàn bộ quyền chi tiết thuộc nhóm cũng bị xóa. Hành động này không thể hoàn tác.',
                variant: 'danger',
                okLabel: 'Xóa',
            })

            if (! dongY) {
                return
            }

            this.$axios.delete(this.route('QuyenController.deleteQuyen', { id_quyen: item.id_quyen }, false))
                .then(() => {
                    this.getQuyen(this.tableQuyen.pagination.current_page)
                })
                .catch((e) => {
                    this.loi = this.thongBaoLoi(e)
                })
        },
        moChiTiet(item) {
            this.nhomDangXem = { id_quyen: item.id_quyen, ten_nhom: item.ten_nhom }
            this.huySuaCT()
            this.tableQuyenCT.list = []
            this.tableQuyenCT.pagination = {}
            this.getQuyenCT()
            this.$refs.modalChiTiet.open()
        },
        getQuyenCT(page = 1) {
            this.$axios.get(this.route('QuyenController.getQuyenCT', { id_quyen: this.nhomDangXem.id_quyen }, false), {
                params: { page },
            }).then((response) => {
                this.tableQuyenCT.list = response.data.data.data
                this.tableQuyenCT.pagination = response.data.data
            })
        },
        luuQuyenCT() {
            const duLieu = {
                tieu_de: this.quyenCT.tieu_de,
                funcs: this.quyenCT.funcs,
                show_views: this.quyenCT.show_views,
            }

            const goi = this.quyenCT.id_quyen_chi_tiet
                ? this.$axios.put(this.route('QuyenController.updateQuyenCT', { id_quyen: this.nhomDangXem.id_quyen, id_quyen_chi_tiet: this.quyenCT.id_quyen_chi_tiet }, false), duLieu)
                : this.$axios.post(this.route('QuyenController.putQuyenCT', { id_quyen: this.nhomDangXem.id_quyen }, false), duLieu)

            goi.then(() => {
                this.huySuaCT()
                this.getQuyenCT(this.tableQuyenCT.pagination.current_page)
            }).catch((e) => {
                this.loiCT = this.thongBaoLoi(e)
            })
        },
        chinhSuaCT(item) {
            this.loiCT = ''
            this.quyenCT = {
                id_quyen_chi_tiet: item.id_quyen_chi_tiet,
                tieu_de: item.tieu_de,
                funcs: item.funcs,
                show_views: item.show_views,
            }
        },
        async xoaCT(item) {
            const dongY = await this.$refs.xacNhan.open({
                title: 'Xóa quyền chi tiết?',
                message: item.tieu_de,
                detail: 'Quyền này cũng bị gỡ khỏi mọi nhóm quyền tài khoản.',
                variant: 'danger',
                okLabel: 'Xóa',
            })

            if (! dongY) {
                return
            }

            this.$axios.delete(this.route('QuyenController.deleteQuyenCT', { id_quyen: this.nhomDangXem.id_quyen, id_quyen_chi_tiet: item.id_quyen_chi_tiet }, false))
                .then(() => {
                    this.huySuaCT()
                    this.getQuyenCT(this.tableQuyenCT.pagination.current_page)
                })
                .catch((e) => {
                    this.loiCT = this.thongBaoLoi(e)
                })
        },
        huySuaCT() {
            this.loiCT = ''
            this.quyenCT = { id_quyen_chi_tiet: '', tieu_de: '', funcs: '', show_views: '' }
        },
        thongBaoLoi(e) {
            return e.response?.data?.message || 'Không thực hiện được, vui lòng thử lại.'
        },
        formatNgay(ngay) {
            if (! ngay) {
                return ''
            }

            return new Intl.DateTimeFormat('vi-VN', {
                dateStyle: 'short',
                timeStyle: 'short',
            }).format(new Date(ngay))
        },
    },
}
</script>

<style scoped>
:deep(.app-col-ten_nhom) {
    width: auto;
    min-width: 240px;
}

:deep(.app-col-so_chi_tiet) {
    width: 110px;
    text-align: center;
}

:deep(.app-col-ngay_cap_nhat) {
    width: 150px;
    white-space: nowrap;
}

:deep(.app-col-funcs),
:deep(.app-col-show_views) {
    font-family: var(--bs-font-monospace);
    font-size: 0.8125rem;
    word-break: break-word;
}

.app-mark {
    padding: 0 0.1em;
    background: var(--bs-warning-bg-subtle, #fff3cd);
    color: inherit;
    border-radius: 2px;
}
</style>
