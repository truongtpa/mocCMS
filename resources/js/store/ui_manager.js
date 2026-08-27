import { defineStore } from 'pinia'

export const useUIManager = defineStore('ui', {
    state: () => ({
        mobileMode: false,
        tableMode: false,
        sidebarOpen: false,
    }),
    getters: {
        isMobile: (state) => {
            return () => state.mobileMode
        },
        isTablet: (state) => {
            return () => state.tableMode
        },
        isSidebarOpen: (state) => state.sidebarOpen,
    },
    actions: {
        setIsMobile(val) {
            this.mobileMode = val == true ? true : false
        },
        setIsTablet(val) {
            this.tableMode = val == true ? true : false
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen
        },
        setSidebarOpen(val) {
            this.sidebarOpen = !!val
        },
    },
})
