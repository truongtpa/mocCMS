import AppTable from './AppTable.vue'
import AppModal from './AppModal.vue'
import AppConfirm from './AppConfirm.vue'
import AppImage from './AppImage.vue'

export { AppTable, AppModal, AppConfirm, AppImage }

/**
 * Đăng ký toàn cục các control dùng chung.
 *
 *   import AppControls from '@/components/controls'
 *   app.use(AppControls)
 */
export default {
    install(app) {
        app.component('AppTable', AppTable)
        app.component('AppModal', AppModal)
        app.component('AppConfirm', AppConfirm)
        app.component('AppImage', AppImage)
    },
}
