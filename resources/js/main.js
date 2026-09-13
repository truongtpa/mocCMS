import { createApp } from 'vue'
import AdminLteVue from '@adminlte/vue'
import '@adminlte/vue/css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'overlayscrollbars/overlayscrollbars.css'
import 'bootstrap'
import App from './App.vue'
import router from './routers.js'

createApp(App)
    .use(AdminLteVue)
    .use(router)
    .mount('#app')
