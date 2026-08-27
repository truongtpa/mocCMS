import LTEContentWrapper from './controls/LTEContentWrapper.vue'
import LTECard from './controls/LTECard.vue'
import LTEModal from './controls/LTEModal.vue'
import LTEModal2 from './controls/LTEModal2.vue'
import LTEInput from './controls/LTEInput.vue'
import YesNoModal from './controls/YesNoModal.vue'

import LTESelectOption from '@/components/controls/LTESelectOption.vue'
import LTEBreadcrumb from '@/components/controls/LTEBreadcrumb.vue'
import BreadCrumb from '@/components/controls/BreadCrumb.vue'
import LTETextArea from '@/components/controls/LTETextArea.vue'
import LTECombobox from '@/components/controls/LTECombobox.vue'
import LTESelect2 from '@/components/controls/LTESelect2.vue'
import LoadingSpinner from '@/components/controls/LoadingSpinner.vue'
import OneDayTimeline from '@/components/controls/OneDayTimeline.vue'
import LTEButton from './controls/LTEButton.vue'
import LTEDate from '@/components/controls/LTEDate.vue'
import LTEHelp from './controls/LTEHelp.vue'
import LTESelect2Option from './controls/LTESelect2Option.vue'
import LTECodeEditor from './controls/LTECodeEditor.vue'
import LTESuggestion from './controls/LTESuggestion.vue'
import TablePro from './controls/TablePro.vue'
import IconButton from './controls/IconButton.vue'
import AppBadge from './controls/AppBadge.vue'
import AppTable from './controls/AppTable.vue'

// Đăng ký toàn cục các component
const globalComponents = {
    install(app) {
        app.component('LTEModal', LTEModal)
        app.component('LTEModal', LTEModal2)
        app.component('LTEContentWrapper', LTEContentWrapper)
        app.component('LTECard', LTECard)
        app.component('LTEInput', LTEInput)
        app.component('LTESelectOption', LTESelectOption)
        app.component('LTESelect2Option', LTESelect2Option)
        app.component('YesNoModal', YesNoModal)

        app.component('LTEBreadcrumb', LTEBreadcrumb)
        app.component('BreadCrumb', BreadCrumb)
        app.component('LTETextArea', LTETextArea)
        app.component('LTECombobox', LTECombobox)
        app.component('LoadingSpinner', LoadingSpinner)
        app.component('LTESelect2', LTESelect2)
        app.component('LTEButton', LTEButton)
        app.component('LTEDate', LTEDate)
        app.component('OneDayTimeline', OneDayTimeline)
        app.component('LTEHelp', LTEHelp)
        app.component('LTECodeEditor', LTECodeEditor)
        app.component('LTESuggestion', LTESuggestion)
        app.component('TablePro', TablePro)
        app.component('IconButton', IconButton)
        app.component('AppBadge', AppBadge)
        app.component('AppTable', AppTable)
    },
}
export default globalComponents
