import LTEContentWrapper from './controls/LTEContentWrapper.vue';
import LTECard from './controls/LTECard.vue';
import LTEModal from './controls/LTEModal.vue';
import LTEInput from './controls/LTEInput.vue';
import YesNoModal from './controls/YesNoModal.vue';
import LTETable from "./controls/LTETable.vue";
import LTETableV2 from "./controls/LTETableV2.vue";
import LoadingSpinner from "./controls/LoadingSpinner.vue";
import LTEModalUpload from './controls/LTEModalUpload.vue';
import LTESelectOptionAddDisable from './controls/LTESelectOptionAddDisable.vue';
import LTEModalSelectUser from './controls/LTEModalSelectUser.vue';
import Select2 from './controls/Select2.vue';


// Đăng ký toàn cục các component
const globalComponents = {
    install(app) {
        app.component('LTEModal', LTEModal);
        app.component('LTEModalSelectUser', LTEModalSelectUser);
        app.component('LTEContentWrapper', LTEContentWrapper);
        app.component('LTECard', LTECard);
        app.component('LTEInput', LTEInput);
        app.component('YesNoModal', YesNoModal);
        app.component('LTETable', LTETable);
        app.component('LTETableV2', LTETableV2);
        app.component('LoadingSpinner', LoadingSpinner);
        app.component('LTEModalUpload', LTEModalUpload);
        app.component('LTESelectOptionAddDisable', LTESelectOptionAddDisable);
        app.component('Select2', Select2);

    }
};
export default globalComponents;
