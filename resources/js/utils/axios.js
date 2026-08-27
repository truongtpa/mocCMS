import { useAuthStore } from '@/store/auth'
import axios from 'axios'
import func from './func'
import Cookies from 'js-cookie'
import { loadingState } from '@/store/loading'

axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
})

const encodeContext = (data) => {
    try {
        return func.encodeWithKey(JSON.stringify(data), Cookies.get('b'))
    } catch {
        return ''
    }
}
axios.interceptors.request.use(
    (config) => {
        if (config.showLoading === true) {
            loadingState.count++
        }
        const authStore = useAuthStore()
        const context = authStore.currentContext
        if (context && context.path) {
            const ctime = Date.now()
            const signature = func.hmacEncrypt(ctime, Cookies.get('b'))

            config.headers['X-Client-Context'] = encodeContext({
                n: context.name,
                p: context.path,
                ctime: ctime + 1,
                signature: signature,
            })
        }

        return config
    },
    (error) => {
        if (error.config?.showLoading) {
            loadingState.count = Math.max(0, loadingState.count - 1)
        }
        return Promise.reject(error)
    },
)

axios.interceptors.response.use(
    (response) => {
        if (response.config?.showLoading) {
            loadingState.count = Math.max(0, loadingState.count - 1)
        }
        return response
    },
    (error) => {
        if (error.config?.showLoading) {
            loadingState.count = Math.max(0, loadingState.count - 1)
        }
        if (error.response && error.response.status === 403) {
            func.toastError(error.response.data.message ?? 'Lỗi')
            return ''
        } else {
            return Promise.reject(error)
        }
    },
)

window.axios = axios
export default axios
