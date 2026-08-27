// src/store/loading.js
import { reactive } from 'vue'

export const loadingState = reactive({
    count: 0,
    get active() {
        return this.count > 0
    },
})
