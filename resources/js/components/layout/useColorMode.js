import { computed, inject, onBeforeUnmount, onMounted, provide, ref, watch } from 'vue'

/**
 * Chế độ màu sáng / tối / theo hệ điều hành.
 *
 * Ghi thuộc tính `data-bs-theme` lên <html> — đúng cơ chế Bootstrap 5.3 và
 * cũng là thứ mà theme.css bắt để đổi token màu.
 *
 * Giá trị được đọc lại sớm bằng đoạn script inline trong app.blade.php nên
 * trang không bị nháy sáng trước khi Vue mount. Khóa localStorage phải khớp
 * với đoạn script đó.
 */
const COLOR_MODE_KEY = Symbol('app-color-mode')
const STORAGE_KEY = 'moccms.theme'
const MODES = ['light', 'dark', 'auto']

/** Gọi đúng một lần, trong AppLayout. */
export function provideColorMode({ initialMode = 'auto' } = {}) {
    const colorMode = ref(MODES.includes(initialMode) ? initialMode : 'auto')
    const systemDark = ref(false)

    const resolvedMode = computed(() =>
        colorMode.value === 'auto' ? (systemDark.value ? 'dark' : 'light') : colorMode.value
    )

    const apply = () => {
        document.documentElement.setAttribute('data-bs-theme', resolvedMode.value)
    }

    let media = null
    const onSystemChange = (e) => {
        systemDark.value = e.matches
    }

    onMounted(() => {
        try {
            const saved = localStorage.getItem(STORAGE_KEY)
            if (MODES.includes(saved)) colorMode.value = saved
        } catch {
            /* localStorage bị chặn — giữ initialMode */
        }

        media = window.matchMedia('(prefers-color-scheme: dark)')
        systemDark.value = media.matches
        media.addEventListener('change', onSystemChange)
        apply()
    })

    onBeforeUnmount(() => media?.removeEventListener('change', onSystemChange))

    watch(colorMode, (mode) => {
        try {
            localStorage.setItem(STORAGE_KEY, mode)
        } catch {
            /* không lưu được thì vẫn đổi được trong phiên hiện tại */
        }
        apply()
    })

    watch(resolvedMode, apply)

    const api = { colorMode, resolvedMode, setColorMode: (m) => { if (MODES.includes(m)) colorMode.value = m } }
    provide(COLOR_MODE_KEY, api)
    return api
}

/** Dùng trong component con nằm dưới AppLayout. */
export function useColorMode() {
    const api = inject(COLOR_MODE_KEY, null)
    if (!api) throw new Error('[layout] useColorMode() phải nằm trong <AppLayout>.')
    return api
}
