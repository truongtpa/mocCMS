<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <router-link :to="{ name: 'router-admin' }" class="brand-link">
            <img :src="`/asset/admin/images/logo.png`" alt="VLUTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8" />
            <span class="brand-text font-weight-light"><b>HỒ SƠ SINH VIÊN</b></span>
        </router-link>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                    <!-- Group 1: CHỨC NĂNG -->
                    <li class="nav-header">CHỨC NĂNG</li>

                    <li v-if="checkPermission('StudentPortalController.getDashboardStats')" class="nav-item">
                        <router-link :to="{ name: 'router-admin' }" class="nav-link" exact>
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Bảng điều khiển</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('StudentPortalController.getProfileData')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-ho-so' }" class="nav-link">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>Thông tin cá nhân</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('StudentPortalController.getAchievementsList')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-thanh-tich' }" class="nav-link">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Giải thưởng & Thành tích</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('StudentPortalController.getBookingsList')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-dat-lich' }" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Đặt lịch Giảng viên</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('DynamicObjectController.getTypes')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-doi-tuong-dong' }" class="nav-link">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Đối tượng & Thuộc tính</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('PhanQuyenController.getDanhSachVaiTro') || checkPermission('PhanQuyenController.getMaTranQuyen')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-phan-quyen' }" class="nav-link">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Quản lý Phân quyền</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('PhanQuyenController.getCaiDat')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-cai-dat' }" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Cài đặt</p>
                        </router-link>
                    </li>

                    <li v-if="checkPermission('DynamicObjectController.getDsNhatKy')" class="nav-item">
                        <router-link :to="{ name: 'router-portal-nhat-ky' }" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Nhật ký & Sao lưu</p>
                        </router-link>
                    </li>
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
import { useAuthStore } from '@/store/auth';
import { useUIManager } from '@/store/ui_manager';

export default {
    name: 'LTESidebar',
    setup() {
        const authStore = useAuthStore();
        const uiStore = useUIManager();

        const checkPermission = (permKey) => {
            return authStore.hasPermission(permKey);
        };

        return {
            authStore,
            uiStore,
            checkPermission
        };
    }
};
</script>
