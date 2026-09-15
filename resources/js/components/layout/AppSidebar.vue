<template>
    <aside class="app-sidebar" :data-bs-theme="theme">
        <slot name="brand">
            <div class="sidebar-brand">
                <component :is="brandTag" v-bind="brandProps" class="brand-link">
                    <img v-if="logo" :src="logo" alt="" class="brand-image">
                    <span v-else-if="brandIcon" class="brand-icon">
                        <i :class="['bi', brandIcon]" aria-hidden="true"></i>
                    </span>
                    <span class="brand-text">
                        <slot name="logo">{{ brandText }}</slot>
                    </span>
                </component>
            </div>
        </slot>

        <!-- Khu tài khoản: nằm ngay dưới thanh thương hiệu, menu xổ xuống -->
        <div v-if="user" class="sidebar-user dropdown">
            <button
                type="button"
                class="user-link"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <img v-if="user.image" :src="user.image" class="user-image rounded-circle" :alt="user.name">
                <span v-else class="user-avatar" aria-hidden="true">{{ userInitial }}</span>
                <span class="user-text">
                    <strong>{{ user.name }}</strong>
                    <small v-if="user.role">{{ user.role }}</small>
                </span>
                <i class="user-caret bi bi-chevron-down" aria-hidden="true"></i>
            </button>

            <ul class="dropdown-menu">
                <slot name="user-menu" :user="user" :logout="onLogout" :profile="onProfile">
                    <li v-if="$slots['user-header']" class="user-header">
                        <slot name="user-header" :user="user" />
                    </li>

                    <li v-if="$slots['user-body']" class="user-body">
                        <slot name="user-body" :user="user" />
                    </li>

                    <li v-if="$slots['user-footer']" class="user-footer">
                        <slot name="user-footer" :user="user" :logout="onLogout" :profile="onProfile" />
                    </li>
                    <template v-else>
                        <li>
                            <button type="button" class="dropdown-item" @click="onProfile">
                                <i class="bi bi-person me-2" aria-hidden="true"></i>Thông tin tài khoản
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger" @click="onLogout">
                                <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Đăng xuất
                            </button>
                        </li>
                    </template>
                </slot>
            </ul>
        </div>

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
    /** Icon Bootstrap dùng thay ảnh logo khi không truyền `logo` */
    brandIcon: { type: String, default: '' },
    logoHref: { type: String, default: '/' },
    brandText: { type: String, default: '' },

    /** Sidebar tối ngay cả khi trang đang ở chế độ sáng — đổi thành 'light' nếu muốn ngược lại */
    theme: { type: String, default: 'dark', validator: (v) => ['dark', 'light'].includes(v) },

    linkComponent: { type: [String, Object, Function], default: 'a' },

    /** null/undefined thì ẩn hẳn khu tài khoản trên đầu sidebar */
    user: { type: Object, default: null },
})

const emit = defineEmits(['logout', 'profile'])

const { isMobile, closeMobile } = useSidebar()

/** Không có ảnh đại diện thì lấy chữ cái đầu của tên làm avatar */
const userInitial = computed(() => (props.user?.name || '?').trim().charAt(0).toUpperCase())

const onLogout = () => emit('logout')
const onProfile = () => emit('profile')

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
