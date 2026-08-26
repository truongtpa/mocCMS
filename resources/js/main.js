import './bootstrap';
import globalComponents from './components/Registers';
import router from './routers.js';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import './utils/toast.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import { ZiggyVue } from 'ziggy-js';
import axios from 'axios';
import * as Sentry from "@sentry/vue";
import { showView, napLaiShowView } from './utils/showView';

// Cấu hình axios để gửi cookie
axios.defaults.withCredentials = true;

// Tạo ứng dụng
const app = createApp(App);

// Khởi tạo Pinia
const pinia = createPinia();
app.use(pinia); // Register Pinia with the app

// Khởi tạo sentry
if (import.meta.env.VITE_APP_ENV === 'production') {
    Sentry.init({
        app,
        dsn: import.meta.env.VITE_SENTRY_LARAVEL_DSN,
    })
}

// Sử dụng các plugin
app.use(Toast, {
    transition: 'Vue-Toastification__bounce',
    maxToasts: 5,
    newestOnTop: true,
    timeout: 2000,
});
app.use(ZiggyVue);
app.use(router);
app.use(globalComponents);

// Thiết lập global properties
app.config.globalProperties.$axios = axios;
app.config.globalProperties.$func = window.func;
app.config.globalProperties.$showView = showView;
app.config.globalProperties.$napLaiShowView = napLaiShowView;
app.config.globalProperties.$__tttk = __tttk;
app.config.globalProperties.$store = store;

app.mount('#app');
