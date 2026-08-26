<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECardAddButton title="Nhóm quyền tài khoản">
                        <template #button>
                            <button v-if="$showView('pageQuyenNhom.them')" type="button" class="btn btn-primary" @click="themMoi">
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
                                               @keyup.enter="getQuyenNhom(route('QuyenNhomController.getQuyenNhom'))"
                                               placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                                        <div class="input-group-append">
                                        <span class="input-group-text"
                                              @click="getQuyenNhom(route('QuyenNhomController.getQuyenNhom'))"
                                              style="cursor: pointer;">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <LTETableV2
                                :data="tableQuyenNhom.list"
                                :headers="tableQuyenNhom.headers"
                                :pagination="tableQuyenNhom.pagination"
                                :fetchPage="getQuyenNhom"
                            >
                                <template #actions="{ item }">
                                    <i v-if="$showView('pageQuyenNhom.sua')" @click="chinhSua(item)" class="fas fa-edit icon-edit"></i>
                                    <i v-if="$showView('pageQuyenNhom.xoa')" @click="deleteQuyenNhom(item)" class="fas fa-trash icon-delete"></i>
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
            <div class="col-8">
                <LTEInput
                    v-model="quyenNhom.tieu_de"
                    label="Tiêu đề"
                    placeholder="Nhập tiêu đề nhóm quyền"
                    type="text">
                </LTEInput>
            </div>
            <div class="col-4">
                <LTESelectOption
                    v-model="quyenNhom.mac_dinh"
                    label="Mặc định"
                    :options="dsMacDinh">
                </LTESelectOption>
            </div>
        </div>
    </LTEModal>
</template>

<script>
import LTEContentWrapper from '@/components/controls/LTEContentWrapper.vue';
import LTECard from '@/components/controls/LTECard.vue';
import LTETableV2 from '@/components/controls/LTETableV2.vue';
import YesNoModal from '@/components/controls/YesNoModal.vue';
import LTEModal from '@/components/controls/LTEModal.vue';
import LTEInput from '@/components/controls/LTEInput.vue';
import LTESelectOption from '@/components/controls/LTESelectOption.vue';
import LTECardAddButton from "@/components/controls/LTECardAddButton.vue";

export default {
    components: {
        LTECardAddButton,
        LTEContentWrapper,
        LTECard,
        LTETableV2,
        YesNoModal,
        LTEModal,
        LTEInput,
        LTESelectOption
    },
    data() {
        return {
            quyenNhom: {
                id_quyen_nhom: '',
                tieu_de: '',
                mac_dinh: 0
            },
            dsMacDinh: [
                { value: 1, text: 'Có' },
                { value: 0, text: 'Không' }
            ],
            tableQuyenNhom: {
                headers: [
                    { key: 'tieu_de', label: 'Tiêu đề', css: 'width: auto' },
                    { key: 'mac_dinh', label: 'Mặc định', css: 'width: 15%' },
                    { key: 'so_chi_tiet', label: 'Số quyền', css: 'width: 15%' },
                    { key: 'ngay_cap_nhat', label: 'Ngày cập nhật', css: 'width: 20%' }
                ],
                pagination: {},
                list: []
            }
        };
    },
    mounted() {
        window.__xemChiTietQuyenNhom = (id) => {
            this.$router.push({
                name: 'router-adminquyennhomct',
                params: { id: id }
            });
        };
        this.getQuyenNhom(route('QuyenNhomController.getQuyenNhom'));
    },
    methods: {
        getQuyenNhom(url) {
            const field = document.querySelector("input[name=search]").value || '';
            const params = { s: field };
            this.$axios.get(url, { params }).then((response) => {
                this.tableQuyenNhom.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableQuyenNhom.list.push({
                        raw: item,
                        tieu_de: `<a class="text-primary" style="text-decoration: none; cursor: pointer;" onclick="window.__xemChiTietQuyenNhom(${item.id_quyen_nhom})">${item.tieu_de}</a>`,
                        mac_dinh: item.mac_dinh === 1
                            ? `<span class="badge bg-success text-white">Có</span>`
                            : `<span class="badge bg-secondary text-white">Không</span>`,
                        so_chi_tiet: `${item.chi_tiet_count ?? 0}`,
                        ngay_cap_nhat: this.$func.formatDate(item.ngay_cap_nhat)
                    });
                });
                this.tableQuyenNhom.pagination = rData;
            });
        },
        async themMoi() {
            this.quyenNhom = { id_quyen_nhom: '', tieu_de: '', mac_dinh: 0 };
            this.$refs.mdThem.$data.title = 'Thêm mới nhóm quyền';
            this.$refs.mdThem.$data.save = 'Lưu thông tin';
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;

            await this.putQuyenNhom();
        },
        async putQuyenNhom() {
            const params = {
                tieu_de: this.quyenNhom.tieu_de,
                mac_dinh: this.quyenNhom.mac_dinh
            };

            await this.$axios.post(route('QuyenNhomController.putQuyenNhom'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyenNhom(route('QuyenNhomController.getQuyenNhom'));
                        this.$func.toastSuccess(response.data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async chinhSua(item) {
            this.quyenNhom = {
                id_quyen_nhom: item.raw.id_quyen_nhom,
                tieu_de: item.raw.tieu_de,
                mac_dinh: item.raw.mac_dinh ?? 0
            };
            this.$refs.mdThem.$data.title = 'Cập nhật nhóm quyền';
            this.$refs.mdThem.$data.save = 'Cập nhật';
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;

            await this.updateQuyenNhom();
        },
        async updateQuyenNhom() {
            const params = {
                id_quyen_nhom: this.quyenNhom.id_quyen_nhom,
                tieu_de: this.quyenNhom.tieu_de,
                mac_dinh: this.quyenNhom.mac_dinh
            };

            await this.$axios.put(route('QuyenNhomController.updateQuyenNhom'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyenNhom(route('QuyenNhomController.getQuyenNhom'));
                        this.$func.toastSuccess(response.data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async deleteQuyenNhom(item) {
            const result = await this.$refs.confirm.openModal();
            if (!result) return;

            const params = { id_quyen_nhom: item.raw.id_quyen_nhom };
            this.$axios.delete(route('QuyenNhomController.deleteQuyenNhom'), { params })
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyenNhom(route('QuyenNhomController.getQuyenNhom'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
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
</style>
