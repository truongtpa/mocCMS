import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        permissions: [],
        routerName: '',
        currentPermission: [],
        currentContext: { name: '', path: '' },
    }),
    actions: {
        setUser(user, ds_quyen) {
            this.user = user
            this.permissions = ds_quyen || []
        },
        logout() {
            this.user = null
            this.permissions = []
            this.routerName = ''
            this.currentPermission = null
        },
        hasPermission(permission) {
            if (this.user && (this.user.is_admin === true || this.user.is_admin == 1)) return true;
            if (!this.permissions || !Array.isArray(this.permissions)) return false;
            return this.permissions.includes(permission) || this.permissions.some((p) => (typeof p === 'string' ? p === permission : p.route === permission || p.ma_quyen === permission))
        },
        findPermission(permission) {
            return this.permissions.find((p) => (typeof p === 'string' ? p === permission : p.route === permission || p.ma_quyen === permission)) || {}
        },
        setRouterName(routerName) {
            this.routerName = routerName
        },
        setRouterContext(routeData) {
            this.currentContext = routeData
        },
    },
    persist: true, // Lưu vào localStorage
})
