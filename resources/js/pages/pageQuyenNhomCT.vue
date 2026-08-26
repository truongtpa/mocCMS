<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECardAddButton :title="`Nhóm quyền / ${tieu_de}`">
                        <template #button>
                            <button type="button" class="btn btn-primary ml-2" @click="themMoi">
                                <i class="fas fa-plus-circle"></i>  Thêm quyền
                            </button>
                        </template>

                        <template #content>
                            <div class="row mb-3">
                                <div class="col-4">
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control float-right" name="search"
                                               @keyup.enter="getQuyenNhomCT(route('QuyenNhomController.getQuyenNhomCT'))"
                                               placeholder="Nhập từ khóa tìm kiếm và nhấn Enter ...">
                                        <div class="input-group-append">
                                        <span class="input-group-text"
                                              @click="getQuyenNhomCT(route('QuyenNhomController.getQuyenNhomCT'))"
                                              style="cursor: pointer;">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-primary table-borderless">
                                    <tr>
                                        <th style="width: 300px">Tiêu đề</th>
                                        <th style="width: 300px">Funcs (Controller.Func)</th>
                                        <th style="width: 100px">Show views</th>
                                        <th style="width: auto"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-borderless"
                                           v-for="(items, ten_nhom) in tableQuyenNhomCT.list"
                                           :key="ten_nhom">
                                    <tr class="nhom-row">
                                        <td colspan="4">
                                            <span class="text-bold">{{ ten_nhom }}</span>
                                            <span class="badge badge-secondary ml-1">{{ items.length }}</span>
                                        </td>
                                    </tr>
                                    <tr v-for="item in items" :key="item.raw.id_quyen_nhom_chi_tiet">
                                        <td>{{ item.raw.tieu_de }}</td>
                                        <td>{{ item.raw.funcs }}</td>
                                        <td>{{ item.raw.show_views }}</td>
                                        <td class="action-slot">
                                            <i @click="deleteQuyenNhomCT(item)" class="fas fa-trash icon-delete"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div v-if="!soDong" class="text-center text-muted d-block p-2">
                                    <span>Chưa có dữ liệu</span>
                                </div>
                            </div>
                        </template>
                    </LTECardAddButton>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <LTECardAddButton title="Người dùng của nhóm">
                        <template #button>
                            <button  type="button" class="btn btn-primary" @click="moChonTaiKhoan">
                                <i class="fas fa-user-plus"></i>  Thêm người dùng
                            </button>
                        </template>

                        <template #content>
                            <div class="row mb-3">
                                <div class="col-4">
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control float-right" v-model="timKiemNguoiDung"
                                               @keyup.enter="getQuyenNhomTK(route('QuyenNhomController.getQuyenNhomTK'))"
                                               placeholder="Tìm theo họ tên hoặc email ...">
                                        <div class="input-group-append">
                                        <span class="input-group-text"
                                              @click="getQuyenNhomTK(route('QuyenNhomController.getQuyenNhomTK'))"
                                              style="cursor: pointer;">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <LTETableV2
                                :data="tableNguoiDung.list"
                                :headers="tableNguoiDung.headers"
                                :pagination="tableNguoiDung.pagination"
                                :fetchPage="getQuyenNhomTK"
                            >
                                <template #actions="{ item }">
                                    <i v-if="$showView('pageQuyenNhomCT.xoa')" @click="deleteQuyenNhomTK(item)" class="fas fa-trash icon-delete"></i>
                                </template>
                            </LTETableV2>
                        </template>
                    </LTECardAddButton>
                </div>
            </div>
        </template>
    </LTEContentWrapper>

    <YesNoModal ref="confirm"></YesNoModal>

    <LTEModal ref="mdTaiKhoan">
        <div class="row mb-2">
            <div class="col-6">
                <div class="input-group">
                    <input type="text" class="form-control" v-model="timKiemTaiKhoan"
                           @keyup.enter="getDsTaiKhoanChon(route('QuyenNhomController.getDsTaiKhoanChon'))"
                           placeholder="Tìm theo họ tên hoặc email rồi nhấn Enter ...">
                    <div class="input-group-append">
                        <span class="input-group-text"
                              @click="getDsTaiKhoanChon(route('QuyenNhomController.getDsTaiKhoanChon'))"
                              style="cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <LTETableV2
            :data="tableChonTaiKhoan.list"
            :headers="tableChonTaiKhoan.headers"
            :pagination="tableChonTaiKhoan.pagination"
            :fetchPage="getDsTaiKhoanChon"
        >
            <template #actions="{ item }">
                <i v-if="item.raw.da_chon === 1" class="fas fa-check icon-da-co" title="Đã có trong nhóm"></i>
                <i v-else @click="putQuyenNhomTK(item)" class="fas fa-plus icon-them" title="Thêm vào nhóm"></i>
            </template>
        </LTETableV2>
    </LTEModal>

    <LTEModal ref="mdThem">
        <div class="row mb-2">
            <div class="col-6">
                <div class="input-group">
                    <input type="text" class="form-control" v-model="timKiemQuyen"
                           @keyup.enter="getDsQuyenChiTiet(route('QuyenNhomController.getDsQuyenChiTiet'))"
                           placeholder="Tìm theo nhóm quyền, tiêu đề hoặc funcs rồi nhấn Enter ...">
                    <div class="input-group-append">
                        <span class="input-group-text"
                              @click="getDsQuyenChiTiet(route('QuyenNhomController.getDsQuyenChiTiet'))"
                              style="cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="bg-primary table-borderless">
                <tr>
                    <th style="width: 100px">Tiêu đề</th>
                    <th style="width: auto">Controller.Funcs</th>
                    <th style="width: 60px"></th>
                </tr>
                </thead>
                <tbody class="table-borderless"
                       v-for="(items, ten_nhom) in dsChonTheoNhom"
                       :key="ten_nhom">
                <tr class="nhom-row">
                    <td colspan="3">
                        <span class="text-bold">{{ ten_nhom }}</span>
                        <span class="badge badge-secondary ml-1">{{ items.length }}</span>
                    </td>
                </tr>
                <tr v-for="item in items" :key="item.raw.id_quyen_chi_tiet">
                    <td>{{ item.raw.tieu_de }}</td>
                    <td>{{ item.raw.funcs }}</td>
                    <td class="text-right">
                        <i v-if="item.raw.da_chon === 1" class="fas fa-check icon-da-co" title="Đã có trong nhóm"></i>
                        <i v-else @click="putQuyenNhomCT(item)" class="fas fa-plus icon-them" title="Thêm vào nhóm"></i>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-if="!tableChonQuyen.list.length" class="text-center text-muted d-block p-2">
                <span>Không có quyền chi tiết nào</span>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                Có tổng cộng <b>{{ tableChonQuyen.pagination.total || 0 }}</b> dòng dữ liệu.
            </div>
            <div class="col-6">
                <ul v-if="tableChonQuyen.pagination.last_page > 1"
                    class="pagination pagination-sm m-0 float-right">
                    <li class="page-item" :class="{ disabled: !tableChonQuyen.pagination.prev_page_url }">
                        <a class="page-link" href="#"
                           @click.prevent="getDsQuyenChiTiet(tableChonQuyen.pagination.prev_page_url)">Trang trước</a>
                    </li>
                    <li v-for="trang in tableChonQuyen.pagination.last_page" :key="trang" class="page-item"
                        :class="{ active: tableChonQuyen.pagination.current_page === trang }">
                        <a class="page-link" href="#"
                           @click.prevent="getDsQuyenChiTiet(`${tableChonQuyen.pagination.path}?page=${trang}`)">{{ trang }}</a>
                    </li>
                    <li class="page-item" :class="{ disabled: !tableChonQuyen.pagination.next_page_url }">
                        <a class="page-link" href="#"
                           @click.prevent="getDsQuyenChiTiet(tableChonQuyen.pagination.next_page_url)">Trang sau</a>
                    </li>
                </ul>
            </div>
        </div>
    </LTEModal>
</template>

<script>
import LTEContentWrapper from '@/components/controls/LTEContentWrapper.vue';
import LTECard from '@/components/controls/LTECard.vue';
import YesNoModal from '@/components/controls/YesNoModal.vue';
import LTEModal from '@/components/controls/LTEModal.vue';
import LTETableV2 from '@/components/controls/LTETableV2.vue';
import LTECardAddButton from "@/components/controls/LTECardAddButton.vue";

export default {
    components: {
        LTECardAddButton,
        LTEContentWrapper,
        LTECard,
        LTETableV2,
        YesNoModal,
        LTEModal
    },
    data() {
        return {
            id_quyen_nhom: this.$route.params.id,
            tieu_de: '',
            timKiemQuyen: '',
            tableChonQuyen: {
                pagination: {},
                list: []
            },
            tableQuyenNhomCT: {
                list: {}
            },
            timKiemNguoiDung: '',
            tableNguoiDung: {
                headers: [
                    { key: 'ho_ten', label: 'Họ tên', css: 'width: 500px' },
                    { key: 'ten_don_vi', label: 'Đơn vị', css: 'width: 250px' },
                    { key: 'ten_chuc_vu', label: 'Chức vụ', css: 'width: auto' }
                ],
                pagination: {},
                list: []
            },
            timKiemTaiKhoan: '',
            tableChonTaiKhoan: {
                headers: [
                    { key: 'ho_ten', label: 'Họ tên (Email)', css: 'width: 40%' },
                    { key: 'ten_don_vi', label: 'Đơn vị', css: 'width: 35%' }
                ],
                pagination: {},
                list: []
            }
        };
    },
    computed: {
        dsChonTheoNhom() {
            const nhom = {};
            this.tableChonQuyen.list.forEach((item) => {
                const ten = item.raw.ten_nhom || 'Chưa có nhóm';
                if (!nhom[ten]) nhom[ten] = [];
                nhom[ten].push(item);
            });
            return nhom;
        },
        soDong() {
            return Object.values(this.tableQuyenNhomCT.list).reduce((tong, items) => tong + items.length, 0);
        }
    },
    mounted() {
        this.getQuyenNhomCT(route('QuyenNhomController.getQuyenNhomCT'));
        this.getQuyenNhomTK(route('QuyenNhomController.getQuyenNhomTK'));
    },
    methods: {
        getDsQuyenChiTiet(url) {
            const params = { s: this.timKiemQuyen, id_quyen_nhom: this.id_quyen_nhom };
            this.$axios.get(url, { params }).then((response) => {
                this.tableChonQuyen.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableChonQuyen.list.push({
                        raw: item,
                        ten_nhom: item.ten_nhom,
                        tieu_de: item.tieu_de,
                        funcs: item.funcs
                    });
                });
                this.tableChonQuyen.pagination = rData;
            });
        },
        getQuyenNhomCT(url) {
            const field = document.querySelector("input[name=search]").value || '';
            const params = { s: field, id_quyen_nhom: this.id_quyen_nhom };
            this.$axios.get(url, { params }).then((response) => {
                this.tableQuyenNhomCT.list = {};
                this.tieu_de = response.data.data?.tieu_de || '';
                const rData = response.data.data?.nhom_quyen || {};
                for (const [ten_nhom, items] of Object.entries(rData)) {
                    this.tableQuyenNhomCT.list[ten_nhom] = items.map(item => ({ raw: item }));
                }
            });
        },
        async themMoi() {
            this.timKiemQuyen = '';
            this.getDsQuyenChiTiet(route('QuyenNhomController.getDsQuyenChiTiet'));
            this.$refs.mdThem.$data.title = 'Chọn quyền cho nhóm';
            this.$refs.mdThem.$data.save = 'Lưu thay đổi';
            await this.$refs.mdThem.openModal();
            this.$refs.mdThem.closeModal();
        },
        putQuyenNhomCT(item) {
            const params = {
                id_quyen_nhom: this.id_quyen_nhom,
                id_quyen_chi_tiet: item.raw.id_quyen_chi_tiet
            };

            this.$axios.post(route('QuyenNhomController.putQuyenNhomCT'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        item.raw.da_chon = 1;
                        this.getQuyenNhomCT(route('QuyenNhomController.getQuyenNhomCT'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async deleteQuyenNhomCT(item) {
            const result = await this.$refs.confirm.openModal();
            if (!result) return;

            const params = { id_quyen_nhom_chi_tiet: item.raw.id_quyen_nhom_chi_tiet };
            this.$axios.delete(route('QuyenNhomController.deleteQuyenNhomCT'), { params })
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyenNhomCT(route('QuyenNhomController.getQuyenNhomCT'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        getQuyenNhomTK(url) {
            const params = { s: this.timKiemNguoiDung, id_quyen_nhom: this.id_quyen_nhom };
            this.$axios.get(url, { params }).then((response) => {
                this.tableNguoiDung.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableNguoiDung.list.push({
                        raw: item,
                        ho_ten: item.ho_ten + " (" +  item.email + ")",
                        ten_don_vi: item.ten_don_vi,
                        ten_chuc_vu: item.ten_chuc_vu
                    });
                });
                this.tableNguoiDung.pagination = rData;
            });
        },
        async moChonTaiKhoan() {
            this.timKiemTaiKhoan = '';
            this.getDsTaiKhoanChon(route('QuyenNhomController.getDsTaiKhoanChon'));
            this.$refs.mdTaiKhoan.$data.title = 'Thêm người dùng vào nhóm';
            this.$refs.mdTaiKhoan.$data.save = 'Đóng';
            await this.$refs.mdTaiKhoan.openModal();
            this.$refs.mdTaiKhoan.closeModal();
        },
        getDsTaiKhoanChon(url) {
            const params = { s: this.timKiemTaiKhoan, id_quyen_nhom: this.id_quyen_nhom };
            this.$axios.get(url, { params }).then((response) => {
                this.tableChonTaiKhoan.list = [];
                const rData = response.data.data;
                rData.data.forEach((item) => {
                    this.tableChonTaiKhoan.list.push({
                        raw: item,
                        ho_ten: item.ho_ten + " (" + item.email + ")",
                        ten_don_vi: item.ten_don_vi
                    });
                });
                this.tableChonTaiKhoan.pagination = rData;
            });
        },
        putQuyenNhomTK(item) {
            const params = {
                id_quyen_nhom: this.id_quyen_nhom,
                id_tai_khoan: item.raw.id_tai_khoan
            };

            this.$axios.post(route('QuyenNhomController.putQuyenNhomTK'), params)
                .then((response) => {
                    if (response.data.status === 200) {
                        item.raw.da_chon = 1;
                        this.getQuyenNhomTK(route('QuyenNhomController.getQuyenNhomTK'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
        async deleteQuyenNhomTK(item) {
            const result = await this.$refs.confirm.openModal();
            if (!result) return;

            const params = { id_quyen_nhom_tai_khoan: item.raw.id_quyen_nhom_tai_khoan };
            this.$axios.delete(route('QuyenNhomController.deleteQuyenNhomTK'), { params })
                .then((response) => {
                    if (response.data.status === 200) {
                        this.getQuyenNhomTK(route('QuyenNhomController.getQuyenNhomTK'));
                        this.$func.toastSuccess(response.data.message);
                    } else {
                        this.$func.toastError(response.data);
                    }
                })
;
        },
    }
};
</script>

<style scoped>
.action-slot {
    text-align: right;
    white-space: nowrap;
}

.icon-them {
    font-size: 11px;
    background: #28a745;
    color: white;
    padding: 5px;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
    cursor: pointer;
    display: inline-block;
}

.icon-da-co {
    font-size: 11px;
    background: #adb5bd;
    color: white;
    padding: 5px;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    line-height: 16px;
    text-align: center;
    display: inline-block;
}

.nhom-row td {
    background: #f4f6f9;
}

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
</style>
