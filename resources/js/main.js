import { createApp } from 'vue'
import axios from 'axios'
import AdminLteVue from '@adminlte/vue'
import '@adminlte/vue/css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'overlayscrollbars/overlayscrollbars.css'
import 'bootstrap'
import App from './App.vue'
import router from './routers.js'
import { ZiggyVue } from 'ziggy-js'
import AppControls from '@/components/controls'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

createApp(App)
    .use(AdminLteVue)
    .use(router)
    .use(ZiggyVue)
    .use(AppControls)
    .use({
        install(app) {
            app.config.globalProperties.$axios = axios
        },
    })
    .mount('#app')
