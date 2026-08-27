import './bootstrap'
import './assets/css/style.css'
import globalComponents from './components/Registers'
import router from './routers.js'
import { createApp } from 'vue'
import App from './App.vue'
import { ZiggyVue } from 'ziggy-js'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import * as Sentry from '@sentry/vue'
import './utils/monaco'

const app = createApp(App)

if (import.meta.env.APP_ENV === 'production') {
    Sentry.init({
        app,
        dsn: 'https://a4adac9a68079242541b34f68241d01e@sentry.vlute.edu.vn/6',
    })
}

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
app.use(pinia)
app.use(ZiggyVue)
app.use(router)
app.use(globalComponents)
app.config.globalProperties.$axios = window.axios
app.config.globalProperties.$func = window.func
app.mount('#app')
