<template>
    <LTENavbar />

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
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <Suspense>
        <template #default>
            <router-view v-slot="{ Component, route }">
                <keep-alive v-if="route.meta.keepAlive">
                    <component :is="Component" />
                </keep-alive>
                <component v-else :is="Component" />
            </router-view>
        </template>
        <template #fallback> </template>
    </Suspense>

    <!-- Footer -->
    <div v-once>
        <LTEFooter />
    </div>
</template>

<script>
import LTEFooter from '@/components/themes/LTEFooter.vue';
import LTENavbar from '@/components/themes/LTENavbar.vue';
import { useAuthStore } from '@/store/auth';
import { useUIManager } from '@/store/ui_manager';
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';

export default {
    name: 'App',
    components: {
        LTENavbar,
        LTEFooter,
    },
    setup() {
        const authStore = useAuthStore();
        const uiStore = useUIManager();
        const route = useRoute();

        // Close sidebar on navigation on mobile
        watch(() => route.name, () => {
            uiStore.setSidebarOpen(false);
        });

        const isStudent = computed(() => {
            const email = authStore.user?.email || '';
            return email.includes('student.vlute.edu.vn') || email.includes('st.vlute.edu.vn');
        });

        const checkPermission = (permKey) => {
            return authStore.hasPermission(permKey);
        };

        return {
            authStore,
            uiStore,
            isStudent,
            checkPermission
        };
    },
    mounted() {
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                console.log('Trang được khôi phục từ BFCache. Đang buộc làm mới...');
                window.location.reload();
            } else {
                console.log('Tải trang thông thường.');
            }
        });
    },
};
</script>

<style>
@import '@/assets/css/style.css';

/* Left floating sidebar styling matching AdminLTE Dark Theme */
.left-floating-sidebar,
.left-floating-sidebar .sidebar-body {
    background-color: transparent !important;
    color: #c2c7d0;
}
.left-floating-sidebar {
    position: fixed;
    left: 0;
    top: 64px; /* Docked directly under navbar */
    bottom: 0;
    width: 240px;
    z-index: 1000;
    background-color: #1e293b !important; /* Premium Dark Slate */
    border-right: 1px solid #334155;
    transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.sidebar-header {
    background-color: #1e293b !important;
    border-bottom: 1px solid #334155 !important;
}

.sidebar-group-header {
    color: #64748b !important;
    letter-spacing: 0.05em;
    font-size: 0.7rem;
    font-weight: 700;
}

/* Sidebar Overlay */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(15, 23, 42, 0.6);
    z-index: 1055;
    backdrop-filter: blur(4px);
    transition: opacity 0.3s ease;
}

/* Page container background light slate */
.content-wrapper {
    background-color: #f8fafc !important;
    margin-top: 64px !important;
    min-height: calc(100vh - 64px) !important;
    padding: 1rem 0.25rem !important;
}

/* Sidebar navigation links */
.sidebar-link, .sidebar-link:hover, .sidebar-link:focus, .sidebar-link:active {
    text-decoration: none !important;
}
.sidebar-link {
    color: #cbd5e1 !important;
    transition: all 0.2s ease-in-out;
    border: 1px solid transparent;
    border-radius: 8px !important;
}
.sidebar-link i {
    color: #94a3b8 !important;
    transition: color 0.2s ease;
}
.sidebar-link:hover {
    color: #ffffff !important;
    background-color: #334155 !important;
}
.sidebar-link:hover i {
    color: #38bdf8 !important;
}
.active-link {
    color: #ffffff !important;
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
    font-weight: 600;
}
.active-link i {
    color: #ffffff !important;
}

/* Desktop layout with left docked sidebar */
@media (min-width: 769px) {
    .content-wrapper,
    .main-footer {
        margin-left: 240px !important;
        transition: margin-left 0.3s ease-in-out;
    }
    .main-header {
        margin-left: 0 !important;
    }
}

/* Mobile responsive layout */
@media (max-width: 768px) {
    .content-wrapper,
    .main-footer {
        margin-left: 0 !important;
    }
    .left-floating-sidebar {
        position: fixed;
        left: -300px !important;
        top: 0 !important;
        bottom: 0 !important;
        width: 280px !important;
        z-index: 1060 !important;
        border-radius: 0 !important;
        border: none !important;
        border-right: 1px solid #4f5962 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
    }
    .left-floating-sidebar.sidebar-open {
        left: 0 !important;
    }
    .sidebar-header {
        border-radius: 0 !important;
    }
}
</style>
