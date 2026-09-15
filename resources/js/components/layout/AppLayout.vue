<template>
    <div
        :class="[
            'app-wrapper',
            {
                'is-floating': floating,
                'sidebar-collapse': isCollapsed,
                'sidebar-open': isMobileOpen,
            },
        ]"
    >
        <AppTopbar
            :user="user"
            :color-mode-toggle="colorModeToggle"
            :fullscreen="fullscreen"
            @logout="$emit('logout')"
            @profile="$emit('profile')"
        >
            <template #start><slot name="topbar-start" /></template>
            <template #end><slot name="topbar-end" /></template>

            <template v-if="$slots['user-menu']" #user-menu="p"><slot name="user-menu" v-bind="p" /></template>
            <template v-if="$slots['user-header']" #user-header="p"><slot name="user-header" v-bind="p" /></template>
            <template v-if="$slots['user-body']" #user-body="p"><slot name="user-body" v-bind="p" /></template>
            <template v-if="$slots['user-footer']" #user-footer="p"><slot name="user-footer" v-bind="p" /></template>
        </AppTopbar>

        <AppSidebar
            :items="menuItems"
            :current-path="currentPath"
            :logo="logo"
            :logo-href="logoHref"
            :brand-text="brandText"
            :theme="sidebarTheme"
            :link-component="linkComponent"
        >
            <template v-if="$slots['sidebar-brand']" #brand><slot name="sidebar-brand" /></template>
            <template v-if="$slots.logo" #logo><slot name="logo" /></template>
            <template v-if="$slots.sidebar" #default><slot name="sidebar" /></template>
        </AppSidebar>

        <main class="app-main">
            <slot />
        </main>

        <footer class="app-footer">
            <div v-if="footerRightText || $slots['footer-right']" class="float-end d-none d-sm-inline">
                <slot name="footer-right">{{ footerRightText }}</slot>
            </div>
            <slot name="footer" />
        </footer>

        <!-- Lớp phủ mobile: bấm ra ngoài để đóng sidebar -->
        <div class="sidebar-overlay" role="presentation" @click="closeMobile"></div>
    </div>
</template>

<script setup>
import AppTopbar from './AppTopbar.vue'
import AppSidebar from './AppSidebar.vue'
import { provideSidebar } from './useSidebar'
import { provideColorMode } from './useColorMode'

/**
 * Khung trang quản trị: sidebar trái, thanh trên, nội dung, chân trang.
 *
 * Thay cho <LteDashboardLayout> của @adminlte/vue. Giữ nguyên tên prop và
 * cấu trúc menu-items nên các trang không phải sửa gì.
 *
 * Lưới CSS và toàn bộ style khung nằm ở resources/css/layout.css.
 */
const props = defineProps({
    /** Mảng item: xem AppNavItem.vue để biết ba dạng header / item / group */
    menuItems: { type: Array, default: () => [] },

    /** Đường dẫn hiện tại, thường là $route.path — dùng để tô sáng mục đang mở */
    currentPath: { type: String, default: '/' },

    logo: { type: String, default: '' },
    logoHref: { type: String, default: '/' },
    brandText: { type: String, default: '' },
    /** 'auto' | 'dark' | 'light' — xem AppSidebar.vue */
    sidebarTheme: { type: String, default: 'dark' },

    /** Truyền RouterLink để menu đi bằng vue-router thay vì tải lại trang */
    linkComponent: { type: [String, Object, Function], default: 'a' },

    user: { type: Object, default: null },
    colorModeToggle: { type: Boolean, default: true },
    fullscreen: { type: Boolean, default: true },

    footerRightText: { type: String, default: '' },

    /**
     * Tách sidebar và thanh trên thành hai mặt phẳng bo góc, cách nhau một khe
     * hở, thay vì dính liền thành một khối. Phần style ở cuối layout.css.
     */
    floating: { type: Boolean, default: false },

    /** Ngưỡng (px) chuyển sang kiểu mobile: sidebar trượt đè + lớp phủ */
    sidebarBreakpoint: { type: Number, default: 992 },
    initialColorMode: { type: String, default: 'auto' },
})

defineEmits(['logout', 'profile'])

const { isCollapsed, isMobileOpen, closeMobile } = provideSidebar({
    breakpoint: props.sidebarBreakpoint,
})
provideColorMode({ initialMode: props.initialColorMode })
</script>
