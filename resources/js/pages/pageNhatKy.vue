<template>
    <LTEContentWrapper>
        <template #content>
            <div class="row">
                <div class="col-12">
                    <LTECard title="Hệ thống / Nhật ký hoạt động">
                        <template #content>
                            <div class="nhat-ky">
                                <div class="form-row mb-2">
                                    <div class="col-lg-4 col-12 mb-1">
                                        <div class="input-group">
                                            <input type="text" class="form-control" v-model="boLoc.s"
                                                   @keyup.enter="timKiem"
                                                   placeholder="Tìm tên người dùng, email hoặc hành động ...">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor: pointer;" @click="timKiem">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-12 mb-1">
                                        <select class="form-control" v-model="boLoc.action" @change="timKiem">
                                            <option value="">Tất cả hành động</option>
                                            <option v-for="hd in dsHanhDong" :key="hd.action" :value="hd.action">
                                                {{ hd.mo_ta }} ({{ hd.so_lan }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-6 mb-1">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Từ</span>
                                            </div>
                                            <input type="date" class="form-control" v-model="boLoc.tu_ngay" @change="timKiem">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-6 mb-1">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Đến</span>
                                            </div>
                                            <input type="date" class="form-control" v-model="boLoc.den_ngay" @change="timKiem">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-12 mb-1">
                                        <button class="btn btn-default btn-block" @click="xoaBoLoc" title="Xóa bộ lọc">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>

                                <LTETableV2
                                    :data="tableNhatKy.list"
                                    :headers="tableNhatKy.headers"
                                    :pagination="tableNhatKy.pagination"
                                    :fetchPage="loadNhatKy"
                                    trackBy="raw.id_logs">
                                </LTETableV2>
                            </div>
                        </template>
                    </LTECard>
                </div>
            </div>
        </template>
    </LTEContentWrapper>
</template>

<script>
export default {
    data() {
        return {
            boLoc: {
                s: '',
                action: '',
                tu_ngay: '',
                den_ngay: ''
            },
            dsHanhDong: [],
            tableNhatKy: {
                headers: [
                    { key: 'col1', label: 'Thời gian', css: 'width: 150px' },
                    { key: 'col2', label: 'Người dùng', css: 'width: 300px' },
                    { key: 'col3', label: 'Hành động', css: 'width: 350px' },
                    { key: 'col4', label: 'Tham số' }
                ],
                pagination: {},
                list: []
            }
        };
    },
    mounted() {
        this.loadNhatKy(route('NhatKyController.getDanhSach'));
        this.loadHanhDong();
    },
    methods: {
        timKiem() {
            this.loadNhatKy(route('NhatKyController.getDanhSach'));
        },
        xoaBoLoc() {
            this.boLoc = { s: '', action: '', tu_ngay: '', den_ngay: '' };
            this.timKiem();
        },
        loadHanhDong() {
            this.$axios.get(route('NhatKyController.getDsHanhDong')).then((response) => {
                this.dsHanhDong = response.data.data ?? [];
            });
        },
        loadNhatKy(url) {
            this.$axios.get(url, { params: { ...this.boLoc } }).then((response) => {
                const rData = response.data.data;
                this.tableNhatKy.list = rData.data.map((item) => ({
                    'raw': item,
                    'col1': this.$func.formatDate(item.ngay_tao, 'DD/MM/YYYY HH:mm'),
                    'col2': item.ho_ten ? `${this.escape(item.ho_ten)}` : '<span class="text-muted">Khách / chưa đăng nhập</span>',
                    'col3': item.mo_ta === item.action
                        ? this.escape(item.action)
                        : `${this.escape(item.mo_ta)} (${this.escape(item.action)})`,
                    'col4': this.oThamSo(item.parms)
                }));
                this.tableNhatKy.pagination = rData;
            });
        },

        oThamSo(parms) {
            const chuoi = (parms ?? '').trim();
            if (!chuoi || chuoi === '{}' || chuoi === '[]') {
                return '<span class="text-muted"></span>';
            }
            const rutGon = chuoi.length > 80 ? chuoi.slice(0, 80) + '…' : chuoi;
            return `<span class="text-muted" title="${this.escape(chuoi)}">${this.escape(rutGon)}</span>`;
        },

        escape(chuoi) {
            return String(chuoi ?? '').replace(/[&<>"']/g, (k) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            })[k]);
        }
    }
};
</script>
