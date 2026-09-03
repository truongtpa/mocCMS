<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                    <i class="fas fa-user-circle text-secondary mr-2" style="font-size: 1.8rem;"></i>
                    <span class="font-weight-bold text-dark d-none d-sm-inline">{{ authStore.user?.ho_ten }}</span>
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
                        <a href="#" class="btn btn-sm btn-danger px-3" @click.prevent="handleLogout">
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
