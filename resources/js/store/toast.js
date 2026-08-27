import { reactive } from 'vue'

export const toastState = reactive({
    toasts: []
})

const activeTimers = new Map()

export const toast = {
    add(message, type = 'success', timeout = 3000) {
        const id = Math.random().toString(36).substring(2, 9) + Date.now().toString(36)
        const newToast = {
            id,
            message,
            type,
            timeout,
            remaining: timeout,
            lastStartedAt: Date.now()
        }
        toastState.toasts.push(newToast)

        if (timeout > 0) {
            toast.startTimer(id, timeout)
        }
        return id
    },
    startTimer(id, delay) {
        toast.clearTimer(id)
        const timerId = setTimeout(() => {
            toast.remove(id)
        }, delay)
        activeTimers.set(id, timerId)
    },
    clearTimer(id) {
        if (activeTimers.has(id)) {
            clearTimeout(activeTimers.get(id))
            activeTimers.delete(id)
        }
    },
    pause(id) {
        const item = toastState.toasts.find(t => t.id === id)
        if (item && item.timeout > 0 && activeTimers.has(id)) {
            toast.clearTimer(id)
            item.remaining -= (Date.now() - item.lastStartedAt)
            console.log(`[Toast] Paused ${id}, remaining: ${item.remaining}ms`)
        }
    },
    resume(id) {
        const item = toastState.toasts.find(t => t.id === id)
        if (item && item.timeout > 0 && !activeTimers.has(id)) {
            item.lastStartedAt = Date.now()
            const delay = Math.max(1000, item.remaining)
            toast.startTimer(id, delay)
            console.log(`[Toast] Resumed ${id}, delay: ${delay}ms`)
        }
    },
    success(message, timeout = 3000) {
        return toast.add(message, 'success', timeout)
    },
    error(message, timeout = 4000) {
        return toast.add(message, 'error', timeout)
    },
    warning(message, timeout = 3500) {
        return toast.add(message, 'warning', timeout)
    },
    info(message, timeout = 3000) {
        return toast.add(message, 'info', timeout)
    },
    remove(id) {
        toast.clearTimer(id)
        const index = toastState.toasts.findIndex(t => t.id === id)
        if (index !== -1) {
            toastState.toasts.splice(index, 1)
        }
    }
}
