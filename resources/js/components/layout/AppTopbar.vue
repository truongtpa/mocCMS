<template>
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button type="button" class="nav-link" title="Ẩn/hiện menu" @click="toggle">
                        <i class="bi bi-list" aria-hidden="true"></i>
                    </button>
                </li>
                <slot name="start" />
            </ul>

            <ul class="navbar-nav ms-auto">
                <slot name="end" />

                <li v-if="fullscreen" class="nav-item">
                    <button
                        type="button"
                        class="nav-link"
                        :title="isFullscreen ? 'Thoát toàn màn hình' : 'Toàn màn hình'"
                        @click="toggleFullscreen"
                    >
                        <i :class="['bi', isFullscreen ? 'bi-fullscreen-exit' : 'bi-arrows-fullscreen']" aria-hidden="true"></i>
                    </button>
                </li>

                <li v-if="colorModeToggle" class="nav-item dropdown">
                    <button
                        type="button"
                        class="nav-link"
                        title="Chế độ màu"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i :class="['bi', currentModeIcon]" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li v-for="mode in modes" :key="mode.value">
                            <button
                                type="button"
                                :class="['dropdown-item d-flex align-items-center', { active: colorMode === mode.value }]"
                                @click="setColorMode(mode.value)"
                            >
                                <i :class="['bi', mode.icon, 'me-2']" aria-hidden="true"></i>
                                {{ mode.label }}
                                <i v-if="colorMode === mode.value" class="bi bi-check-lg ms-auto" aria-hidden="true"></i>
                            </button>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useSidebar } from './useSidebar'
import { useColorMode } from './useColorMode'

/**
 * Thanh trên cùng: nút thu gọn sidebar, chế độ màu, toàn màn hình.
 * Menu tài khoản nằm trên đầu sidebar, xem AppSidebar.vue.
 *
 * Dropdown chạy bằng JS của Bootstrap qua `data-bs-toggle="dropdown"`, nên
 * main.js phải còn dòng `import 'bootstrap'`.
 */
defineProps({
    colorModeToggle: { type: Boolean, default: true },
    fullscreen: { type: Boolean, default: true },
})

const { toggle } = useSidebar()
const { colorMode, setColorMode } = useColorMode()

const modes = [
    { value: 'light', icon: 'bi-sun-fill', label: 'Sáng' },
    { value: 'dark', icon: 'bi-moon-fill', label: 'Tối' },
    { value: 'auto', icon: 'bi-circle-half', label: 'Theo hệ thống' },
]

const currentModeIcon = computed(
    () => modes.find((m) => m.value === colorMode.value)?.icon ?? 'bi-circle-half'
)

const isFullscreen = ref(false)
const syncFullscreen = () => {
    isFullscreen.value = document.fullscreenElement !== null
}

onMounted(() => document.addEventListener('fullscreenchange', syncFullscreen))
onBeforeUnmount(() => document.removeEventListener('fullscreenchange', syncFullscreen))

function toggleFullscreen() {
    // Người dùng có thể đã chặn fullscreen — nuốt lỗi, không làm vỡ trang
    if (document.fullscreenElement) document.exitFullscreen().catch(() => {})
    else document.documentElement.requestFullscreen().catch(() => {})
}
</script>
