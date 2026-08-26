<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECardAddButton title="Quản lý / Phòng">
                        <template #button>
                            <button v-if="$showView('pagePhong.them')"
                                    type="button" class="btn btn-primary" @click="themMoi">
                                <i class="fas fa-plus-circle"></i> 
                                Thêm mới
                            </button>
                        </template>
                        <template #content>
                            <div class="row mb-3">
                                <div class="col-4">
                                    <!-- Nút thêm mới -->
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control float-right" name="search"
                                               @keyup.enter="loadphong(route('PhongController.loadPhong'))"
                                               placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                                        <div class="input-group-append">
                                            <span class="input-group-text"
                                                  @click="loadphong(route('PhongController.loadPhong'))"
                                                  style="cursor: pointer;">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bảng dữ liệu -->
                            <LTETableV2
                                :data="tablephong.list"
                                :headers="tablephong.headers"
                                :pagination="tablephong.pagination"
                                :fetchPage="loadphong">
                                <template #actions="{ item }">
                                    <i v-if="$showView('pagePhong.sua')"
                                       @click="chinhSua(item)" class="fas fa-edit icon-edit"></i>
                                    <i v-if="$showView('pagePhong.xoa')"
                                       @click="xoaphong(item)" class="fas fa-trash icon-delete"></i>
                                </template>
                            </LTETableV2>
                        </template>
                    </LTECardAddButton>
                </div>
            </div>
        </template>
    </LTEContentWrapper>

    <!-- Modal xác nhận xóa -->
    <YesNoModal ref="confirm"></YesNoModal>

    <!-- Modal thêm/sửa -->
    <LTEModal ref="mdThem">
        <div class="row">
            <div class="col-12">
                <LTEInput
                    v-model="phong.ten_phong"
                    label="Tên phòng"
                    placeholder="Nhập tên phòng"
                    type="text">
                </LTEInput>
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
            phong: {
                ten_phong: '',
                id_phong: ''
            },
            tablephong: {
                headers: [
                    { key: 'col1', label: 'ID', css: 'width: 100px' },
                    { key: 'col2', label: 'Tên phòng', css: '' },
                ],
                pagination: {},
                list: []
            }

        };
    },
    mounted() {
        this.loadphong(route('PhongController.loadPhong'));
    },
    methods: {
        loadphong(url) {
            const field = document.querySelector("input[name=search]").value;
            const params = { 's': field };
            this.$axios.get(url, { params: params }).then((response) => {
                this.tablephong.list = [];
                const rData = response.data.data;
                rData.data.forEach((item, index) => {
                    this.tablephong.list.push({
                        'raw': item,
                        'col1': index + 1,
                        'col2': item.ten_phong,
                    });
                });
                this.tablephong.pagination = rData;
            });
        },
        async themMoi() {
            this.phong = { ten_phong: '', id_phong: '' };
            this.$refs.mdThem.$data.title = "Thêm mới thông tin";
            this.$refs.mdThem.$data.save = "Lưu thông tin";
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;
            const params = {
                'ten_phong': this.phong.ten_phong,
            };
            await this.$axios.post(route('PhongController.them'), params)
                .then((response) => {
                    let data = response.data;
                    if (data.status === 200) {
                        this.loadphong(route('PhongController.loadPhong'));
                        this.$func.toastSuccess(data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.$func.toastError(data.errors);
                    }
                });
        },
        async xoaphong(item) {
            const result = await this.$refs.confirm.openModal();
            if (!result) return;

            const params = { 'id_phong': item.raw.id_phong };
            this.$axios.delete(route('PhongController.xoa'), { params: params })
                .then((response) => {
                    const data = response.data;
                    if (data.status === 200) {
                        this.loadphong(route('PhongController.loadPhong'));
                        this.$func.toastSuccess(data.message);
                    } else {
                        this.$func.toastError(data.message);
                    }
                });
        },
        async chinhSua(item) {
            this.phong.id_phong = item.raw.id_phong;
            this.phong.ten_phong = item.raw.ten_phong;

            this.$refs.mdThem.$data.title = "Cập nhật tên phòng";
            this.$refs.mdThem.$data.save = "Cập nhật";
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;

            const params = {
                'ten_phong': this.phong.ten_phong,
                'id_phong': this.phong.id_phong,
            };
            this.$axios.put(route('PhongController.capNhat'), params)
                .then((response) => {
                    const data = response.data;
                    if (data.status === 200) {
                        this.loadphong(route('PhongController.loadPhong'));
                        this.$func.toastSuccess(data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.$func.toastError(data.errors);
                    }
                });
        },
    }
};
</script>

<style>
.dropdown-toggle::after {
    content: unset !important;
}
.dropdown-custom {
    padding: 0 !important;
    margin: 0 !important;
    border: 0;
    height: 30px;
    width: 30px;
    border-radius: 30px;
    background: #007bff;
    color: white;
    font-size: 12px;
}
</style>
