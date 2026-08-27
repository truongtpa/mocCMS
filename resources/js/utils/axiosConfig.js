import axios from 'axios'
import { keycloak } from '@/keycloak'
import func from '@/utils/func'
import { useAuthStore } from '@/store/auth'

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    },
})

apiClient.interceptors.request.use(
    async (config) => {
        if (keycloak.authenticated) {
            if (keycloak.isTokenExpired()) {
                try {
                    await keycloak.updateToken(30)
                } catch (error) {
                    console.error('Token refresh failed', error)
                }
            }
            const token = keycloak.token
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    },
)

apiClient.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            func.toastError('Session expired. Redirecting to login page.')
            return ''
        } else {
            return Promise.reject(error)
        }
    },
)

export default apiClient
