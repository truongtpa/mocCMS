import AppLayout from './AppLayout.vue'
import AppSidebar from './AppSidebar.vue'
import AppTopbar from './AppTopbar.vue'
import AppNavItem from './AppNavItem.vue'
import AppContent from './AppContent.vue'
import AppCard from './AppCard.vue'

export { AppLayout, AppSidebar, AppTopbar, AppNavItem, AppContent, AppCard }
export { useSidebar } from './useSidebar'
export { useColorMode } from './useColorMode'

/**
 * Đăng ký toàn cục bộ khung giao diện.
 *
 *   import AppLayoutPlugin from '@/components/layout'
 *   app.use(AppLayoutPlugin)
 *
 * AppSidebar / AppTopbar / AppNavItem đã được AppLayout dùng sẵn bên trong,
 * đăng ký thêm ở đây chỉ để tiện khi cần dựng khung khác (ví dụ trang đăng nhập).
 */
export default {
    install(app) {
        app.component('AppLayout', AppLayout)
        app.component('AppSidebar', AppSidebar)
        app.component('AppTopbar', AppTopbar)
        app.component('AppNavItem', AppNavItem)
        app.component('AppContent', AppContent)
        app.component('AppCard', AppCard)
    },
}
