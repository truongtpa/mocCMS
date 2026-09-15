import { computed, inject, onBeforeUnmount, onMounted, provide, ref, watch } from 'vue'

/**
 * Trạng thái sidebar dùng chung cho AppLayout / AppTopbar / AppSidebar.
 *
 * Hai trạng thái tách biệt vì hành vi khác nhau:
 *  - desktop: thu gọn (đẩy sidebar ra khỏi màn) — có nhớ qua localStorage
 *  - mobile : mở đè lên nội dung kèm lớp phủ — luôn đóng lại khi đổi trang
 */
const SIDEBAR_KEY = Symbol('app-sidebar')
const STORAGE_KEY = 'moccms.sidebar'

/** Gọi đúng một lần, trong AppLayout. */
export function provideSidebar({ breakpoint = 992, persist = true } = {}) {
    const isCollapsed = ref(false)
    const isMobileOpen = ref(false)
    const windowWidth = ref(typeof window === 'undefined' ? 9999 : window.innerWidth)

    const isMobile = computed(() => windowWidth.value <= breakpoint)

    const onResize = () => {
        windowWidth.value = window.innerWidth
    }

    onMounted(() => {
        window.addEventListener('resize', onResize, { passive: true })

        if (!persist) return
        try {
            const saved = localStorage.getItem(STORAGE_KEY)
            if (saved !== null) isCollapsed.value = JSON.parse(saved) === true
        } catch {
            /* localStorage bị chặn (private mode) — bỏ qua, dùng mặc định */
        }
    })

    onBeforeUnmount(() => window.removeEventListener('resize', onResize))

    watch(isCollapsed, (collapsed) => {
        if (!persist) return
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(collapsed))
        } catch {
            /* không lưu được thì thôi, không phải lỗi chặn luồng */
        }
    })

    // Kéo từ mobile sang desktop mà quên đóng thì trạng thái mobile treo lại
    watch(isMobile, (mobile) => {
        if (!mobile) isMobileOpen.value = false
    })

    const toggle = () => {
        if (isMobile.value) isMobileOpen.value = !isMobileOpen.value
        else isCollapsed.value = !isCollapsed.value
    }

    /** Đóng lớp phủ mobile. Trên desktop không làm gì. */
    const closeMobile = () => {
        isMobileOpen.value = false
    }

    const api = { isCollapsed, isMobileOpen, isMobile, toggle, closeMobile }
    provide(SIDEBAR_KEY, api)
    return api
}

/** Dùng trong component con nằm dưới AppLayout. */
export function useSidebar() {
    const api = inject(SIDEBAR_KEY, null)
    if (!api) throw new Error('[layout] useSidebar() phải nằm trong <AppLayout>.')
    return api
}
