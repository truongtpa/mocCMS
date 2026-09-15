<template>
    <aside class="app-sidebar" :data-bs-theme="theme === 'auto' ? null : theme">
        <slot name="brand">
            <div class="sidebar-brand">
                <component :is="brandTag" v-bind="brandProps" class="brand-link">
                    <img v-if="logo" :src="logo" alt="" class="brand-image">
                    <span class="brand-text">
                        <slot name="logo">{{ brandText }}</slot>
                    </span>
                </component>
            </div>
        </slot>

        <div class="sidebar-wrapper">
            <!--
                Bắt click ở đây thay vì gắn handler lên từng mục: menu lồng nhau
                nhiều cấp thì truyền sự kiện ngược lên rất phiền.
            -->
            <nav aria-label="Điều hướng chính" @click="onNavClick">
                <slot>
                    <ul class="sidebar-menu">
                        <AppNavItem
                            v-for="item in items"
                            :key="item.type === 'item' ? item.href : `${item.type}:${item.text}`"
                            :item="item"
                            :current-path="currentPath"
                            :link-component="linkComponent"
                        />
                    </ul>
                </slot>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue'
import AppNavItem from './AppNavItem.vue'
import { useSidebar } from './useSidebar'

const props = defineProps({
    items: { type: Array, default: () => [] },
    currentPath: { type: String, default: '/' },

    logo: { type: String, default: '' },
    logoHref: { type: String, default: '/' },
    brandText: { type: String, default: '' },

    /**
     * 'auto'  — đi theo chế độ màu của trang (không gắn data-bs-theme)
     * 'dark'  — luôn tối, kể cả khi trang đang sáng (kiểu admin cổ điển)
     * 'light' — luôn sáng
     */
    theme: {
        type: String,
        default: 'dark',
        validator: (v) => ['auto', 'dark', 'light'].includes(v),
    },

    linkComponent: { type: [String, Object, Function], default: 'a' },
})

const { isMobile, closeMobile } = useSidebar()

// Logo cũng đi qua router nếu trang dùng RouterLink, để khỏi tải lại cả app
const brandTag = computed(() => props.linkComponent)
const brandProps = computed(() =>
    typeof props.linkComponent === 'string' ? { href: props.logoHref } : { to: props.logoHref }
)

/** Trên mobile, chọn xong một mục thì đóng sidebar lại cho thấy nội dung. */
function onNavClick(e) {
    if (!isMobile.value) return
    if (e.target.closest('a.nav-link')) closeMobile()
}
</script>
