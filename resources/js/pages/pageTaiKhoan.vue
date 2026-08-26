<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECardAddButton title="Thông tin / Tài khoản">
                        <template #button>
                                    <button v-if="$showView('pageTaiKhoan.them')"
                                            type="button" class="btn btn-primary" @click="themMoi">
                                        <i class="fas fa-plus-circle"></i> 
                                        Thêm mới
                                    </button>
                        </template>
                        <template #content>
                            <div class="row mb-3">
                                <div class="col-4">
                                </div>
                                <div class="col-4">

                                    <div class="input-group">
                                        <input type="text" class="form-control float-right" name="search"
                                            @keyup.enter="loadTaiKhoan(route('TaiKhoanController.danhsach'))"
                                               placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                                        <div class="input-group-append">
                                            <span class="input-group-text"
                                                  @click="loadTaiKhoan(route('TaiKhoanController.danhsach'))"
                                                  style="cursor: pointer;">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div @click="onTableClick">
                                <LTETableV2
                                    :data="tableTaiKhoan.list"
                                    :headers="tableTaiKhoan.headers"
                                    :pagination="tableTaiKhoan.pagination"
                                    :fetchPage="loadTaiKhoan">
                                    <template #actions="{ item }">
                                        <i v-if="$showView('pageTaiKhoan.sua')"
                                           @click="chinhSua(item)" class="fas fa-edit icon-edit"></i>
                                        <i v-if="$showView('pageTaiKhoan.xoa')"
                                           @click="xoaTaiKhoan(item)" class="fas fa-trash icon-delete"></i>
                                    </template>
                                </LTETableV2>
                            </div>
                        </template>
                    </LTECardAddButton>
                </div>
            </div>
        </template>
    </LTEContentWrapper>
    <YesNoModal ref="confirm"></YesNoModal>
    <LTEModal ref="mdThem">
        <div class="row">
            <div class="col-6">
                <LTEInput v-model="taiKhoan.ho_ten" label="Họ tên" placeholder="Họ tên" type="text"></LTEInput>
            </div>
            <div class="col-6">
                <LTEInput v-model="taiKhoan.email" label="Email" placeholder="Email" type="text"></LTEInput>
            </div>
        </div>

        <!-- Chức vụ theo đơn vị, lưu ngay khi thêm/gỡ -->
        <template v-if="taiKhoan.id_tai_khoan">
            <hr>
            <label>Chức vụ theo đơn vị</label>
            <div class="row align-items-end">
                <div class="col-md-5">
                    <LTESelectOption v-model="chucVuForm.id_don_vi" :options="optionDonVi" label="Đơn vị"></LTESelectOption>
                </div>
                <div class="col-md-5">
                    <LTESelectOption v-model="chucVuForm.id_chuc_vu" :options="optionChucVu" label="Chức vụ"></LTESelectOption>
                </div>
                <div class="col-md-2 form-group">
                    <button type="button" class="btn btn-primary btn-block" :disabled="dangLuuChucVu" @click="themChucVu">
                        <i class="fas fa-plus-circle"></i> Thêm
                    </button>
                </div>
            </div>

            <table class="table table-sm chuc-vu-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px">STT</th>
                        <th>Đơn vị</th>
                        <th style="width: 35%">Chức vụ</th>
                        <th style="width: 60px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in dsChucVu" :key="item.id_tai_khoan_chuc_vu">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.ten_don_vi || '—' }}</td>
                        <td>
                            {{ item.ten_chuc_vu || '—' }}
                            <span v-if="Number(item.truong_don_vi) === 1" class="badge badge-info">Trưởng đơn vị</span>
                        </td>
                        <td class="text-center">
                            <i class="fas fa-trash icon-delete" title="Gỡ chức vụ" @click="xoaChucVu(item)"></i>
                        </td>
                    </tr>
                    <tr v-if="!dsChucVu.length">
                        <td colspan="4" class="text-center text-muted py-4">Tài khoản chưa được gán chức vụ nào.</td>
                    </tr>
                </tbody>
            </table>
        </template>
        <small v-else class="text-muted">Lưu tài khoản xong rồi mở lại để gán chức vụ theo đơn vị.</small>
    </LTEModal>
</template>

<script>
import LTESelectOption from "../components/controls/LTESelectOption.vue";
import LTECard from "../components/controls/LTECard.vue";
import LTETableV2 from "../components/controls/LTETableV2.vue";
import YesNoModal from "../components/controls/YesNoModal.vue";
import LTEModal from "../components/controls/LTEModal.vue";
import LTECardAddButton from "@/components/controls/LTECardAddButton.vue";

export default {
    components: {
        LTECardAddButton,
        LTESelectOption,
        LTECard,
        LTETableV2,
        YesNoModal,
        LTEModal
    },
    data() {
        return {
            taiKhoan: {
                ho_ten: '',
                email: '',
                id_tai_khoan: ''
            },
            optionDonVi: [],
            optionChucVu: [],
            dsChucVu: [],
            chucVuForm: { id_don_vi: '', id_chuc_vu: '' },
            dangLuuChucVu: false,
            tableTaiKhoan: {
                headers: [
                    { key: 'col1', label: 'Họ tên', css: 'width: 20%' },
                    { key: 'col2', label: 'Email', css: 'width: 20%' },
                    { key: 'col3', label: 'Chức vụ (theo đơn vị)', css: 'width: 35%' },
                    { key: 'col4', label: 'Đơn vị', css: 'width: 25%' },
                ],
                pagination: {},
                list: []
            }

        };
    },
    mounted() {
        this.loadTaiKhoan(route('TaiKhoanController.danhsach'));
        this.fetchDonVi();
        this.fetchChucVu();
    },
    methods: {
        moTaChucVu(list) {
            if (!Array.isArray(list) || !list.length) return '<span class="text-muted">Chưa có chức vụ</span>';

            return list
                .map(item => `${item.ten_chuc_vu || '—'} <span class="text-muted">(${item.ten_don_vi || 'chưa rõ đơn vị'})</span>`)
                .join('<br>');
        },
        onTableClick(event) {
            const link = event.target.closest('a.link-chuc-vu');
            if (!link) return;

            event.preventDefault();
            const item = this.tableTaiKhoan.list.find(row => String(row.raw.id_tai_khoan) === link.dataset.id);
            if (item) this.chinhSua(item);
        },
        // Nạp chức vụ hiện có và danh mục đơn vị/chức vụ cho modal cập nhật.
        async loadChucVu(taiKhoan) {
            const { data } = await this.$axios.get(route('TaiKhoanController.getChucVu'), {
                params: { id_tai_khoan: taiKhoan.id_tai_khoan }
            });
            if (data.status !== 200) return this.$func.toastError(this.errorText(data));

            this.dsChucVu = data.data.danh_sach;
            this.optionDonVi = data.data.don_vi.map(item => ({ value: Number(item.id_don_vi), text: item.ten_don_vi }));
            this.optionChucVu = data.data.chuc_vu.map(item => ({ value: Number(item.id_chuc_vu), text: item.ten_chuc_vu }));
            // Mặc định gán tại đơn vị của tài khoản cho nhanh.
            this.chucVuForm = { id_don_vi: taiKhoan.id_don_vi ? Number(taiKhoan.id_don_vi) : '', id_chuc_vu: '' };
        },
        async themChucVu() {
            if (!this.chucVuForm.id_don_vi || !this.chucVuForm.id_chuc_vu) {
                return this.$func.toastError('Vui lòng chọn đơn vị và chức vụ');
            }
            this.dangLuuChucVu = true;
            try {
                const { data } = await this.$axios.post(route('TaiKhoanController.themChucVu'), {
                    id_tai_khoan: this.taiKhoan.id_tai_khoan,
                    id_don_vi: this.chucVuForm.id_don_vi,
                    id_chuc_vu: this.chucVuForm.id_chuc_vu
                });
                if (data.status !== 200) return this.$func.toastError(this.errorText(data));
                this.dsChucVu = data.data;
                this.chucVuForm.id_chuc_vu = '';
                this.$func.toastSuccess(data.message);
            } catch (error) {
                console.error('Error adding chuc vu:', error);
            } finally {
                this.dangLuuChucVu = false;
            }
        },
        async xoaChucVu(item) {
            const { data } = await this.$axios.delete(route('TaiKhoanController.xoaChucVu'), {
                params: { id_tai_khoan_chuc_vu: item.id_tai_khoan_chuc_vu }
            });
            if (data.status !== 200) return this.$func.toastError(this.errorText(data));
            this.dsChucVu = data.data;
            this.$func.toastSuccess(data.message);
        },
        errorText(data) {
            return Array.isArray(data.errors) ? data.errors.join(', ') : (data.errors || data.message || 'Có lỗi xảy ra');
        },
        async fetchDonVi() {
            const url = route('DonViController.danhSachDonVi');
            const response = await this.$axios.get(url);
            const rData = response.data.data;

            this.optionDonVi = rData.map(item => ({
                value: Number(item.id_don_vi),
                text: item.ten_don_vi
            }));
        },
        async fetchChucVu() {
            const url = route('ChucVuController.danhSachChucVu');
            const response = await this.$axios.get(url);
            const rData = response.data.data;

            this.optionChucVu = rData.map(item => ({
                value: Number(item.id_chuc_vu),
                text: item.ten_chuc_vu
            }));
        },
        async themMoi() {
            this.taiKhoan = { ho_ten: '', email: '', id_tai_khoan: '' };
            this.dsChucVu = [];
            this.$refs.mdThem.$data.title = "Thêm mới thông tin";
            this.$refs.mdThem.$data.save = "Lưu thông tin";
            const result = await this.$refs.mdThem.openModal();
            if (!result) return;
            const params = {
                'ho_ten': this.taiKhoan.ho_ten,
                'email': this.taiKhoan.email,
            };
            await this.$axios.post(route('TaiKhoanController.them'), params)
                .then((response) => {
                    const data = response.data;
                    if (data.status === 200) {
                        this.loadTaiKhoan(route('TaiKhoanController.danhsach'));
                        this.$func.toastSuccess(data.message);
                        this.$refs.mdThem.closeModal();
                    } else {
                        this.themMoi();
                        this.$func.toastError(data);
                    }
                });
        },
        loadTaiKhoan(url) {
            const field = document.querySelector("input[name=search]").value;
            const params = { 's': field };
            this.$axios.get(url, { params }).then((response) => {
                this.tableTaiKhoan.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableTaiKhoan.list.push({
                        'raw': item,
                        'col1': `<a class="link-chuc-vu" href="javascript:void(0)" data-id="${item.id_tai_khoan}">${item.ho_ten}</a>`,
                        'col2': item.email,
                        'col3': this.moTaChucVu(item.chuc_vu),
                        'col4': item.ten_don_vi,
                    });
                });
                this.tableTaiKhoan.pagination = rData;
            });
        },
        async xoaTaiKhoan(item) {
            const result = await this.$refs.confirm.openModal();
            if (!result) return;
            const params = { 'id_tai_khoan': item.raw.id_tai_khoan };
            this.$axios.delete(route('TaiKhoanController.xoa'), { params })
                .then((response) => {
                    const data = response.data;
                    if (data.status === 200) {
                        this.loadTaiKhoan(route('TaiKhoanController.danhsach'));
                        this.$func.toastSuccess(data.message);
                    } else {
                        this.$func.toastError(data.message);
                    }
                });
        },
        async chinhSua(item) {
            await this.loadChucVu(item.raw);

            this.taiKhoan = {
                id_tai_khoan: item.raw.id_tai_khoan,
                ho_ten: item.raw.ho_ten,
                email: item.raw.email
            };

            this.$refs.mdThem.$data.title = "Cập nhật thông tin";
            this.$refs.mdThem.$data.save = "Cập nhật";

            const result = await this.$refs.mdThem.openModal();

            if (!result) {
                // Chức vụ đã lưu ngay khi thêm/gỡ nên vẫn phải làm mới bảng.
                this.loadTaiKhoan(route('TaiKhoanController.danhsach'));

                return;
            }

            const params = {
                ho_ten: this.taiKhoan.ho_ten,
                email: this.taiKhoan.email,
                id_tai_khoan: this.taiKhoan.id_tai_khoan
            };

            try {
                const response = await this.$axios.put(
                    route('TaiKhoanController.capNhat'),
                    params
                );

                const data = response.data;

                if (data.status === 200) {
                    await this.loadTaiKhoan(
                        route('TaiKhoanController.danhsach')
                    );

                    this.$func.toastSuccess(data.message);
                    this.$refs.mdThem.closeModal();
                } else {
                    this.$func.toastError(data);
                }
            } catch (error) {
                console.error(
                    'Error updating account:',
                    error.response?.data || error
                );

            }
        },
    }
};
</script>

<style>
.chuc-vu-table th, .chuc-vu-table td { border-left: 0; border-right: 0; }
.chuc-vu-table thead th { border-top: 0; border-bottom: 2px solid #dee2e6; }
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
