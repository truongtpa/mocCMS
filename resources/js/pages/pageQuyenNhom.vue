<template>
    <AppContent>
        <br>
        <AppCard title="Nhóm quyền tài khoản / Danh sách">
            <div v-if="loi" class="alert alert-danger py-2">{{ loi }}</div>

            <AppTable
                :items="tableQuyenNhom.list"
                :columns="columns"
                :pagination="tableQuyenNhom.pagination"
                :highlight="boLoc.daApDung"
                item-key="id_quyen_nhom"
                empty-text="Chưa có nhóm quyền tài khoản nào."
                @page-change="getQuyenNhom"
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
                            placeholder="Nhập tiêu đề hoặc mã vai trò và nhấn Enter ..."
                            @keyup.enter="timKiem"
                        >
                        <button type="button" class="btn btn-default" title="Tìm kiếm" @click="timKiem">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </template>

                <template #cell-tieu_de="{ item, highlight }">
                    <RouterLink :to="{ name: 'router-quyennhomct', params: { id: item.id_quyen_nhom } }">
                        <template v-for="(phan, i) in highlight(item.tieu_de)" :key="i"><mark
                            v-if="phan.hit"
                            class="app-mark"
                        >{{ phan.text }}</mark><template v-else>{{ phan.text }}</template></template>
                    </RouterLink>
                </template>

                <template #cell-ma_vai_tro="{ value }">
                    <code v-if="value">{{ value }}</code>
                    <span v-else class="text-body-secondary">—</span>
                </template>

                <template #cell-mac_dinh="{ value }">
                    <span class="badge" :class="value ? 'text-bg-success' : 'text-bg-secondary'">
                        {{ value ? 'Có' : 'Không' }}
                    </span>
                </template>

                <template #cell-so_chi_tiet="{ value }">
                    <span class="badge text-bg-secondary">{{ value }}</span>
                </template>

                <template #cell-so_tai_khoan="{ value }">
                    <span class="badge text-bg-secondary">{{ value }}</span>
                </template>

                <template #actions="{ item }">
                    <RouterLink
                        :to="{ name: 'router-quyennhomct', params: { id: item.id_quyen_nhom } }"
                        class="btn btn-sm btn-outline-secondary"
                        title="Xem chi tiết"
                    >
                        <i class="bi bi-list-check"></i>
                    </RouterLink>
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
            :title="quyenNhomForm.id_quyen_nhom ? 'Cập nhật nhóm quyền' : 'Thêm nhóm quyền'"
            icon="bi-people"
            :ok-label="quyenNhomForm.id_quyen_nhom ? 'Cập nhật' : 'Lưu thông tin'"
            :auto-close="false"
            @ok="luuQuyenNhom"
        >
            <div v-if="loiForm" class="alert alert-danger py-2">{{ loiForm }}</div>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" for="qn-tieu-de">Tiêu đề</label>
                    <input id="qn-tieu-de" v-model="quyenNhomForm.tieu_de" type="text" class="form-control" placeholder="Nhập tiêu đề nhóm quyền">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="qn-mac-dinh">Mặc định</label>
                    <select id="qn-mac-dinh" v-model.number="quyenNhomForm.mac_dinh" class="form-select">
                        <option :value="0">Không</option>
                        <option :value="1">Có</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="qn-ma-vai-tro">Mã vai trò</label>
                    <input id="qn-ma-vai-tro" v-model="quyenNhomForm.ma_vai_tro" type="text" class="form-control font-monospace" placeholder="Ví dụ: admin">
                </div>
            </div>
        </AppModal>

        <AppConfirm ref="xacNhan" />
    </AppContent>
</template>

<script>
import { RouterLink } from 'vue-router'

export default {
    name: 'pageQuyenNhom',
    components: { RouterLink },
    data() {
        return {
            loi: '',
            loiForm: '',
            boLoc: {
                s: '',
                daApDung: '',
            },
            quyenNhomForm: {
                id_quyen_nhom: '',
                tieu_de: '',
                ma_vai_tro: '',
                mac_dinh: 0,
            },
            tableQuyenNhom: {
                pagination: {},
                list: [],
            },
            columns: [
                { key: 'tieu_de', label: 'Tiêu đề' },
                { key: 'ma_vai_tro', label: 'Mã vai trò' },
                { key: 'mac_dinh', label: 'Mặc định' },
                { key: 'so_chi_tiet', label: 'Số quyền' },
                { key: 'so_tai_khoan', label: 'Số tài khoản' },
                { key: 'ngay_cap_nhat', label: 'Ngày cập nhật', formatter: (v) => this.formatNgay(v) },
            ],
        }
    },
    mounted() {
        this.getQuyenNhom()
    },
    methods: {
        timKiem() {
            this.getQuyenNhom(1)
        },
        getQuyenNhom(page = 1) {
            this.$axios.get(this.route('QuyenNhomController.getQuyenNhom', undefined, false), {
                params: { s: this.boLoc.s, page },
            }).then((response) => {
                this.tableQuyenNhom.list = response.data.data.data
                this.tableQuyenNhom.pagination = response.data.data
                this.boLoc.daApDung = this.boLoc.s
                this.loi = ''
            })
        },
        themMoi() {
            this.quyenNhomForm = { id_quyen_nhom: '', tieu_de: '', ma_vai_tro: '', mac_dinh: 0 }
            this.loiForm = ''
            this.$refs.modalForm.open()
        },
        chinhSua(item) {
            this.quyenNhomForm = {
                id_quyen_nhom: item.id_quyen_nhom,
                tieu_de: item.tieu_de,
                ma_vai_tro: item.ma_vai_tro ?? '',
                mac_dinh: item.mac_dinh ?? 0,
            }
            this.loiForm = ''
            this.$refs.modalForm.open()
        },
        luuQuyenNhom() {
            const duLieu = {
                tieu_de: this.quyenNhomForm.tieu_de,
                ma_vai_tro: this.quyenNhomForm.ma_vai_tro,
                mac_dinh: this.quyenNhomForm.mac_dinh,
            }

            const goi = this.quyenNhomForm.id_quyen_nhom
                ? this.$axios.put(this.route('QuyenNhomController.updateQuyenNhom', { id_quyen_nhom: this.quyenNhomForm.id_quyen_nhom }, false), duLieu)
                : this.$axios.post(this.route('QuyenNhomController.putQuyenNhom', undefined, false), duLieu)

            this.$refs.modalForm.setBusy(true)
            goi.then(() => {
                this.$refs.modalForm.close(true)
                this.getQuyenNhom(this.tableQuyenNhom.pagination.current_page)
            }).catch((e) => {
                this.loiForm = this.thongBaoLoi(e)
            }).finally(() => {
                this.$refs.modalForm.setBusy(false)
            })
        },
        async xoa(item) {
            const dongY = await this.$refs.xacNhan.open({
                title: 'Xóa nhóm quyền tài khoản?',
                message: item.tieu_de,
                detail: 'Toàn bộ quyền và tài khoản đã gán cho nhóm cũng bị gỡ. Hành động này không thể hoàn tác.',
                variant: 'danger',
                okLabel: 'Xóa',
            })

            if (! dongY) {
                return
            }

            this.$axios.delete(this.route('QuyenNhomController.deleteQuyenNhom', { id_quyen_nhom: item.id_quyen_nhom }, false))
                .then(() => {
                    this.getQuyenNhom(this.tableQuyenNhom.pagination.current_page)
                })
                .catch((e) => {
                    this.loi = this.thongBaoLoi(e)
                })
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
:deep(.app-col-tieu_de) {
    width: auto;
    min-width: 220px;
}

:deep(.app-col-ma_vai_tro) {
    width: 140px;
}

:deep(.app-col-mac_dinh) {
    width: 110px;
    text-align: center;
}

:deep(.app-col-so_chi_tiet),
:deep(.app-col-so_tai_khoan) {
    width: 110px;
    text-align: center;
}

:deep(.app-col-ngay_cap_nhat) {
    width: 150px;
    white-space: nowrap;
}

.app-mark {
    padding: 0 0.1em;
    background: var(--bs-warning-bg-subtle, #fff3cd);
    color: inherit;
    border-radius: 2px;
}
</style>
