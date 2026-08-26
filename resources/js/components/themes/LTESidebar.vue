<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <router-link :to="{ name: 'router-home' }" class="brand-link">
            <img alt="" src="./images/logo.png" class="brand-image img-circle elevation-3" style="opacity: .8" />
            <span class="brand-text font-weight-light">   <b>QUẢN LÝ VĂN BẢN</b></span>
        </router-link>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                    <template v-for="nhom in menuHienThi" :key="nhom.header">
                        <li class="nav-header">{{ nhom.header }}</li>

                        <li class="nav-item" v-for="muc in nhom.items" :key="muc.route + muc.view">
                            <router-link :to="{ name: muc.route }" class="nav-link">
                                <i class="nav-icon" :class="muc.icon"></i>
                                <p>{{ muc.label }}</p>
                            </router-link>
                        </li>
                    </template>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<style scoped>
.sidebar {
    min-height: 100vh;
    height: auto;
    overflow-y: auto;
    padding-bottom: 100px;
}
</style>

<script>

export default {
    data() {
        return {
            __tttk: __tttk,
            menu: [
                {
                    header: 'TRÌNH KÝ VĂN BẢN',
                    items: [
                        { view: 'menuGuiTrinhKy.xem', route: 'router-trinhky-guiyeucau', icon: 'fas fa-paper-plane', label: 'Gửi yêu cầu' },
                        { view: 'menuDSTrinhKy.xem', route: 'router-trinhky', icon: 'fas fa-file-signature', label: 'Danh sách trình ký' },
                        { view: 'menuTrinhKyCuaToi.xem', route: 'router-trinhky-cuatoi', icon: 'fas fa-folder-open', label: 'Trình ký của tôi' },
                        { view: 'menuTrinhKyPheDuyet.xem', route: 'router-trinhky-pheduyet', icon: 'fas fa-stamp', label: 'Phê duyệt' },
                        { view: 'menuLoaiVB.xem', route: 'router-loaivanban', icon: 'fas fa-file-alt', label: 'Loại văn bản' }
                    ]
                },
                {
                    header: 'VĂN BẢN',
                    items: [
                        { view: 'menuTimKiemVanBan.xem', route: 'router-allvanban', icon: 'fas fa-search', label: 'Tìm kiếm văn bản' },
                        { view: 'menuQLVanBan.xem', route: 'router-vanban', icon: 'fas fa-folder-open', label: 'Xem văn bản' },
                        { view: 'menuVanBanCXL.xem', route: 'router-vanbanuser', icon: 'fas fa-file-archive', label: 'Văn bản cần xử lý' },
                        { view: 'menuPheDuyetVanBan.xem', route: 'router-vanbanxetduyet', icon: 'fas fa-file-signature', label: 'Phê duyệt văn bản' }
                    ]
                },
                {
                    header: 'THÔNG BÁO',
                    items: [
                        { view: 'menuQLThongBao.xem', route: 'router-thongbao', icon: 'fas fa-list-alt', label: 'Quản lý thông báo' },
                        { view: 'menuDanhMucThongBao.xem', route: 'router-danhmucthongbao', icon: 'fas fa-layer-group', label: 'Danh mục thông báo' }
                    ]
                },
                {
                    header: 'CÔNG TÁC',
                    items: [
                        { view: 'menuQLLichCongTac.xem', route: 'router-adminlichcongtac', icon: 'fas fa-calendar-check', label: 'Quản lý lịch công tác' },
                        { view: 'menuLichCongTac.xem', route: 'router-userlichcongtac', icon: 'fas fa-calendar', label: 'Lịch công tác' }
                    ]
                },
                {
                    header: 'BÁO CÁO',
                    items: [
                        { view: 'menuBaoCaoThang.xem', route: 'router-danhmucbaocaonoibo', icon: 'fas fa-receipt', label: 'Báo cáo tháng' }
                    ]
                },
                {
                    header: 'QUYỀN & TÀI KHOẢN',
                    items: [
                        { view: 'menuTaiKhoan.xem', route: 'router-taikhoan', icon: 'fas fa-user', label: 'Tài khoản' },
                        { view: 'menuDinhNghiaQuyen.xem', route: 'router-adminquyen', icon: 'fas fa-users-cog', label: 'Định nghĩa Quyền' },
                        { view: 'menuNhomQuyenTK.xem', route: 'router-adminquyennhom', icon: 'fas fa-layer-group', label: 'Nhóm Quyền tài khoản' }
                    ]
                },
                {
                    header: 'HỆ THỐNG',
                    items: [
                        { view: 'menuDonVi.xem', route: 'router-donvi', icon: 'fas fa-building', label: 'Đơn vị' },
                        { view: 'menuChucVu.xem', route: 'router-chucvu', icon: 'fas fa-user-tie', label: 'Chức vụ' },
                        { view: 'menuLoaiPheDuyet.xem', route: 'router-loaipheduyet', icon: 'fas fa-check-circle', label: 'Loại phê duyệt' },
                        { view: 'menuLoaiVB.xem', route: 'router-loaivanban', icon: 'fas fa-file-alt', label: 'Loại văn bản' },
                        { view: 'menuPhong.xem', route: 'router-phong', icon: 'fas fa-door-open', label: 'Phòng' },
                        { view: 'menuNhatKy.xem', route: 'router-nhatky', icon: 'fas fa-history', label: 'Nhật ký hoạt động' }
                    ]
                }
            ]
        };
    },
    computed: {
        menuHienThi() {
            return this.menu
                .map(nhom => ({
                    ...nhom,
                    items: nhom.items.filter(muc => this.$showView(muc.view))
                }))
                .filter(nhom => nhom.items.length > 0);
        }
    }
};
</script>
