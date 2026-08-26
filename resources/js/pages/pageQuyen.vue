<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECardAddButton title="Nhóm quyền">
                        <template #button>
                            <button v-if="$showView('pageQuyen.them')" type="button" class="btn btn-primary" @click="themMoi">
                                <i class="fas fa-plus-circle"></i>  Thêm mới
                            </button>
                        </template>

                        <template #content>
                            <div class="row mb-3">
                                <div class="col-4">
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control float-right" name="search"
                                               @keyup.enter="getQuyen(route('QuyenController.getQuyen'))"
                                               placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                                        <div class="input-group-append">
                                        <span class="input-group-text"
                                              @click="getQuyen(route('QuyenController.getQuyen'))"
                                              style="cursor: pointer;">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <LTETableV2
                                :data="tableQuyen.list"
                                :headers="tableQuyen.headers"
                                :pagination="tableQuyen.pagination"
                                :fetchPage="getQuyen"
                            >
                                <template #actions="{ item }">
                                    <i v-if="$showView('pageQuyen.sua')" @click="chinhSua(item)" class="fas fa-edit icon-edit"></i>
                                    <i v-if="$showView('pageQuyen.xoa')" @click="deleteQuyen(item)" class="fas fa-trash icon-delete"></i>
                                </template>
                            </LTETableV2>
                        </template>
                    </LTECardAddButton>
                </div>
            </div>
        </template>
    </LTEContentWrapper>

    <YesNoModal ref="confirm"></YesNoModal>

    <LTEModal ref="mdThem">
        <div class="row">
            <div class="col-12">
                <LTEInput
                    v-model="quyenForm.ten_nhom"
                    label="Tên nhóm quyền"
                    placeholder="Nhập tên nhóm quyền"
                    type="text">
                </LTEInput>
            </div>
        </div>
    </LTEModal>

    <LTEModal ref="mdChiTiet" size="modal-xl modal-chi-tiet">
        <div v-if="ctXoa" class="alert alert-danger d-flex justify-content-between align-items-center">
            <span>Xóa quyền chi tiết "{{ ctXoa.raw.tieu_de }}"?</span>
            <span>
                <button type="button" class="btn btn-sm btn-danger" @click="deleteQuyenCT">Xóa</button>
                <button type="button" class="btn btn-sm btn-default ml-2" @click="ctXoa = null">Hủy</button>
            </span>
        </div>

        <div class="row">
            <div class="col-12">
                <LTEInput
                    v-model="quyenCT.tieu_de"
                    label="Tiêu đề"
                    placeholder="Nhập tiêu đề"
                    type="text">
                </LTEInput>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Funcs (Controller.Func)</label>
                    <textarea
                        v-model="quyenCT.funcs"
                        class="form-control text-monospace"
                        rows="4"
                        placeholder="Nhập funcs"></textarea>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Show views</label>
                    <textarea
                        v-model="quyenCT.show_views"
                        class="form-control text-monospace"
                        rows="4"
                        placeholder="Nhập show views"></textarea>
                </div>
            </div>
            <div class="col-12 mb-3">
                <button v-if="$showView('pageQuyen.ct.luu')" type="button" class="btn btn-primary" @click="luuQuyenCT">
                    <i class="far fa-save"></i>
                    {{ quyenCT.id_quyen_chi_tiet ? 'Cập nhật' : 'Thêm mới' }}
                </button>
                <button v-if="quyenCT.id_quyen_chi_tiet" type="button" class="btn btn-default ml-2" @click="huySuaCT">
                    Hủy
                </button>
            </div>
        </div>

        <LTETableV2
            :data="tableQuyenCT.list"
            :headers="tableQuyenCT.headers"
            :pagination="tableQuyenCT.pagination"
            :fetchPage="getQuyenCT"
        >
            <template #actions="{ item }">
                <i v-if="$showView('pageQuyen.ct.sua')" @click="chinhSuaCT(item)" class="fas fa-edit icon-edit"></i>
                <i v-if="$showView('pageQuyen.ct.xoa')" @click="ctXoa = item" class="fas fa-trash icon-delete"></i>
            </template>
        </LTETableV2>
    </LTEModal>
</template>

<script>
import LTEContentWrapper from '@/components/controls/LTEContentWrapper.vue';
import LTECard from '@/components/controls/LTECard.vue';
import LTETableV2 from '@/components/controls/LTETableV2.vue';
import YesNoModal from '@/components/controls/YesNoModal.vue';
import LTEModal from '@/components/controls/LTEModal.vue';
import LTEInput from '@/components/controls/LTEInput.vue';
import LTECardAddButton from "@/components/controls/LTECardAddButton.vue";

export default {
    components: {
        LTECardAddButton,
        LTEContentWrapper,
        LTECard,
        LTETableV2,
        YesNoModal,
        LTEModal,
        LTEInput
    },
    data() {
        return {
            quyenForm: {
                id_quyen: '',
                ten_nhom: ''
            },
            tableQuyen: {
                headers: [
                    { key: 'ten_nhom', label: 'Nhóm quyền', css: 'width: 400px' },
                    { key: 'so_chi_tiet', label: 'Số quyền', css: 'width: auto' },
                    { key: 'ngay_cap_nhat', label: 'Ngày cập nhật', css: 'width: 200px' }
                ],
                pagination: {},
                list: []
            },
            ctNhom: {
                id_quyen: '',
                ten_nhom: ''
            },
            quyenCT: {
                id_quyen_chi_tiet: '',
                tieu_de: '',
                funcs: '',
                show_views: ''
            },
            ctXoa: null,
            tableQuyenCT: {
                headers: [
                    { key: 'tieu_de', label: 'Tiêu đề', css: 'width: 150px' },
                    { key: 'funcs', label: 'Funcs', css: 'width: 250px' },
                    { key: 'show_views', label: 'Show views' }
                ],
                pagination: {},
                list: []
            }
        };
    },
    mounted() {
        window.__xemChiTietQuyen = (id) => {
            const item = this.tableQuyen.list.find(row => row.raw.id_quyen === id);
            if (item) this.xemChiTiet(item);
        };
        this.getQuyen(route('QuyenController.getQuyen'));
    },
    methods: {
        getQuyen(url) {
            const field = document.querySelector("input[name=search]").value || '';
            const params = { s: field };
            this.$axios.get(url, { params }).then((response) => {
                this.tableQuyen.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableQuyen.list.push({
                        raw: item,
                        ten_nhom: `<a class="text-primary" style="text-decoration: none; cursor: pointer;" onclick="window.__xemChiTietQuyen(${item.id_quyen})">${item.ten_nhom}</a>`,
                        so_chi_tiet: `${item.chi_tiet_count ?? 0}`,
                        ngay_cap_nhat: this.$func.formatDate(item.ngay_cap_nhat)
                    });
                });
                this.tableQuyen.pagination = rData;
            });
        },
        async themMoi() {
            this.quyenForm = { id_quyen: '', ten_nhom: '' };
            this.$refs.mdThem.$data.title = 'Thêm mới nhóm quyền';
            this.$refs.mdThem.$data.save = 'Lưu thông tin';
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;

            await this.putQuyen();
        },
        async putQuyen() {
            const params = {
                ten_nhom: this.quyenForm.ten_nhom
            };

            await this.$axios.post(route('QuyenController.putQuyen'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyen(route('QuyenController.getQuyen'));
                        this.$func.toastSuccess(response.data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async chinhSua(item) {
            this.quyenForm = {
                id_quyen: item.raw.id_quyen,
                ten_nhom: item.raw.ten_nhom
            };
            this.$refs.mdThem.$data.title = 'Cập nhật nhóm quyền';
            this.$refs.mdThem.$data.save = 'Cập nhật';
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;

            await this.updateQuyen();
        },
        async updateQuyen() {
            const params = {
                id_quyen: this.quyenForm.id_quyen,
                ten_nhom: this.quyenForm.ten_nhom
            };

            await this.$axios.put(route('QuyenController.updateQuyen'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyen(route('QuyenController.getQuyen'));
                        this.$func.toastSuccess(response.data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async deleteQuyen(item) {
            const result = await this.$refs.confirm.openModal();
            if (!result) return;

            const params = { id_quyen: item.raw.id_quyen };
            this.$axios.delete(route('QuyenController.deleteQuyen'), { params })
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyen(route('QuyenController.getQuyen'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async xemChiTiet(item) {
            this.ctNhom = { id_quyen: item.raw.id_quyen, ten_nhom: item.raw.ten_nhom };
            this.huySuaCT();
            this.tableQuyenCT.list = [];
            this.tableQuyenCT.pagination = {};
            this.getQuyenCT(route('QuyenController.getQuyenCT'));

            this.$refs.mdChiTiet.$data.title = `Quyền chi tiết / ${this.ctNhom.ten_nhom}`;
            this.$refs.mdChiTiet.$data.save = 'Lưu thay đổi';
            await this.$refs.mdChiTiet.openModal();
            this.$refs.mdChiTiet.closeModal();
            this.getQuyen(route('QuyenController.getQuyen'));
        },
        getQuyenCT(url) {
            const params = { id_quyen: this.ctNhom.id_quyen };
            this.$axios.get(url, { params }).then((response) => {
                this.tableQuyenCT.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableQuyenCT.list.push({
                        raw: item,
                        tieu_de: item.tieu_de,
                        funcs: item.funcs,
                        show_views: item.show_views
                    });
                });
                this.tableQuyenCT.pagination = rData;
            });
        },
        luuQuyenCT() {
            if (this.quyenCT.id_quyen_chi_tiet) {
                this.updateQuyenCT();
            } else {
                this.putQuyenCT();
            }
        },
        putQuyenCT() {
            const params = {
                id_quyen: this.ctNhom.id_quyen,
                tieu_de: this.quyenCT.tieu_de,
                funcs: this.quyenCT.funcs,
                show_views: this.quyenCT.show_views
            };

            this.$axios.post(route('QuyenController.putQuyenCT'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        this.huySuaCT();
                        this.getQuyenCT(route('QuyenController.getQuyenCT'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        chinhSuaCT(item) {
            this.ctXoa = null;
            this.quyenCT = {
                id_quyen_chi_tiet: item.raw.id_quyen_chi_tiet,
                tieu_de: item.raw.tieu_de,
                funcs: item.raw.funcs,
                show_views: item.raw.show_views
            };
        },
        updateQuyenCT() {
            const params = {
                id_quyen_chi_tiet: this.quyenCT.id_quyen_chi_tiet,
                tieu_de: this.quyenCT.tieu_de,
                funcs: this.quyenCT.funcs,
                show_views: this.quyenCT.show_views
            };

            this.$axios.put(route('QuyenController.updateQuyenCT'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        this.huySuaCT();
                        this.getQuyenCT(route('QuyenController.getQuyenCT'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        deleteQuyenCT() {
            const params = { id_quyen_chi_tiet: this.ctXoa.raw.id_quyen_chi_tiet };
            this.$axios.delete(route('QuyenController.deleteQuyenCT'), { params })
                .then((response) => {
                    if (response.data.status === 200) {
                        this.ctXoa = null;
                        this.huySuaCT();
                        this.getQuyenCT(route('QuyenController.getQuyenCT'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        huySuaCT() {
            this.quyenCT = { id_quyen_chi_tiet: '', tieu_de: '', funcs: '', show_views: '' };
        }
    }
};
</script>

<style scoped>
.icon-edit {
    font-size: 11px;
    background: #0c84ff;
    color: white;
    padding: 5px 3px 5px 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
    cursor: pointer;
}

.icon-delete {
    font-size: 11px;
    background: red;
    color: white;
    padding: 5px;
    border-radius: 50%;
    margin-right: 5px;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
    cursor: pointer;
}

.ml-2 {
    margin-left: 0.5rem;
}

:deep(.modal-chi-tiet .modal-body) {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

:deep(.modal-chi-tiet textarea.form-control) {
    font-size: 13px;
    resize: vertical;
}
</style>
