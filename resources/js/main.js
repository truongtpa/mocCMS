import { createApp } from 'vue'
import axios from 'axios'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap'
// layout.css lo bố cục khung, theme.css ghi đè token màu — phải đứng sau cùng
import '../css/layout.css'
import '../css/theme.css'
import App from './App.vue'
import router from './routers.js'
import { ZiggyVue } from 'ziggy-js'
import AppControls from '@/components/controls'
import AppLayoutPlugin from '@/components/layout'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

createApp(App)
    .use(router)
    .use(ZiggyVue)
    .use(AppControls)
    .use(AppLayoutPlugin)
    .use({
        install(app) {
            app.config.globalProperties.$axios = axios
        },
    })
    .mount('#app')
