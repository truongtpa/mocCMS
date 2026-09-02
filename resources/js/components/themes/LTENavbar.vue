<template>
    <nav class="main-header navbar navbar-expand navbar-light bg-white shadow-sm py-2 px-3 fixed-top" style="height: 64px; z-index: 1040; border-bottom: 1px solid #e2e8f0 !important;">
        <!-- Hamburger button for toggling sidebar on mobile -->
        <button 
            v-if="authStore.user?.email" 
            class="btn btn-link text-dark p-0 mr-3 d-md-none" 
            style="font-size: 1.25rem;"
            @click.prevent="uiStore.toggleSidebar"
        >
            <i class="fas fa-bars"></i>
        </button>

        <!-- Logo / Brand -->
        <router-link :to="{ name: 'router-admin' }" class="navbar-brand d-flex align-items-center mr-4">
            <img :src="`/asset/admin/images/logo.png`" alt="VLUTE Logo" style="height: 44px; width: auto;" class="mr-2.5">
            <div class="d-none d-md-flex flex-column justify-content-center">
                <span class="brand-title font-weight-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.2;">ĐẠI HỌC SƯ PHẠM KỸ THUẬT VĨNH LONG</span>
                <span class="brand-subtitle text-primary font-weight-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">CỔNG THÔNG TIN SINH VIÊN</span>
            </div>
        </router-link>

        <!-- User Menu Dropdown -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                    <i class="fas fa-user-circle text-secondary mr-md-2" style="font-size: 1.8rem;"></i>
                    <span class="font-weight-bold text-dark d-none d-md-inline">{{ authStore.user?.ho_ten }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow border-0" style="border-radius: 8px;">
                    <li class="user-header bg-light d-flex flex-column align-items-center justify-content-center p-3 text-center" style="height: auto;">
                        <i class="fas fa-user-circle fa-3x text-secondary mb-2"></i>
                        <h6 class="font-weight-bold text-dark mb-1">{{ authStore.user?.ho_ten }}</h6>
                        <small class="text-muted">{{ authStore.user?.email }}</small>
                    </li>
                    <li class="user-footer bg-white border-top d-flex justify-content-between p-2">
                        <a class="btn btn-sm btn-outline-danger" :href="route('changePassword')">
                            <i class="fas fa-key mr-1"></i> Đổi mật khẩu
                        </a>
                        <a class="btn btn-sm btn-danger px-3" @click.prevent="handleLogout">
                            <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
import { useAuthStore } from '@/store/auth';
import { useUIManager } from '@/store/ui_manager';

export default {
    setup() {
        const authStore = useAuthStore();
        const uiStore = useUIManager();
        return {
            authStore,
            uiStore,
        };
    },
    methods: {
        handleLogout() {
            window.location.href = route('dangXuat');
        }
    }
};
</script>

<style scoped>
.user-menu .dropdown-toggle::after {
    display: none;
}
</style>
