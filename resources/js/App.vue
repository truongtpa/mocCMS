<template>
    <LTENavbar />
    <LTESidebar />

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

    <!-- Global Toast Container -->
    <Toast />
</template>

<script>
import LTEFooter from '@/components/themes/LTEFooter.vue';
import LTENavbar from '@/components/themes/LTENavbar.vue';
import LTESidebar from '@/components/themes/LTESidebar.vue';
import { useAuthStore } from '@/store/auth';
import { useUIManager } from '@/store/ui_manager';
import { computed, watch } from 'vue';
import { useRoute } from 'vue-router';

export default {
    name: 'App',
    components: {
        LTENavbar,
        LTESidebar,
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
