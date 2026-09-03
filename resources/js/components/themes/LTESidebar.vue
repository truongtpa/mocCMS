<template>
    <!-- Sidebar Overlay (Mobile only) -->
    <div 
        v-if="authStore.user?.email && uiStore.isSidebarOpen"
        class="sidebar-overlay d-md-none"
        @click="uiStore.setSidebarOpen(false)"
    ></div>
    
    <!-- Left-Side Floating Sidebar -->
    <div 
        v-if="authStore.user?.email"
        class="left-floating-sidebar shadow-sm d-flex flex-column"
        :class="{ 'sidebar-open': uiStore.isSidebarOpen }"
    >
        <!-- Sidebar Body -->
        <div class="sidebar-body p-2 flex-grow-1 overflow-auto">
            <!-- Close Button for Mobile -->
            <div class="d-md-none d-flex justify-content-end p-2">
                <button 
                    class="btn btn-link text-white-50 p-0" 
                    @click="uiStore.toggleSidebar"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Group 1: CHỨC NĂNG -->
            <div class="sidebar-group-header px-2 pt-2 pb-1 text-uppercase text-xs font-weight-bold">
                CHỨC NĂNG
            </div>
            <div class="task-group mb-2">
                <router-link 
                    v-if="checkPermission('StudentPortalController.getDashboardStats')"
                    :to="{ name: 'router-admin' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                    exact
                >
                    <i class="fas fa-tachometer-alt mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Bảng điều khiển</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('StudentPortalController.getProfileData')"
                    :to="{ name: 'router-portal-ho-so' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-user-cog mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Thông tin cá nhân</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('StudentPortalController.getAchievementsList')"
                    :to="{ name: 'router-portal-thanh-tich' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-trophy mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Giải thưởng & Thành tích</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('StudentPortalController.getBookingsList')"
                    :to="{ name: 'router-portal-dat-lich' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-calendar-check mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Đặt lịch Giảng viên</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('DynamicObjectController.getTypes')"
                    :to="{ name: 'router-portal-doi-tuong-dong' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-boxes mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Đối tượng & Thuộc tính</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('PhanQuyenController.getDanhSachVaiTro') || checkPermission('PhanQuyenController.getMaTranQuyen')"
                    :to="{ name: 'router-portal-phan-quyen' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-user-shield mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Quản lý Phân quyền</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('PhanQuyenController.getCaiDat')"
                    :to="{ name: 'router-portal-cai-dat' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-cog mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Cài đặt</span>
                </router-link>

                <router-link 
                    v-if="checkPermission('DynamicObjectController.getDsNhatKy')"
                    :to="{ name: 'router-portal-nhat-ky' }" 
                    class="sidebar-link d-flex align-items-center px-3 py-2 mb-1 rounded hover-link"
                    active-class="active-link"
                >
                    <i class="fas fa-history mr-2.5" style="width: 20px; font-size: 0.95rem;"></i>
                    <span class="text-xs font-weight-bold">Nhật ký & Sao lưu</span>
                </router-link>
            </div>
        </div>
    </div>
</template>

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
