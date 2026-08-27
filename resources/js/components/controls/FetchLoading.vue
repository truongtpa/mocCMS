<template>
    <transition name="fade">
        <div v-if="shouldRender" class="loading-bar-container">
            <div class="progress-bar" :style="{ width: displayProgress + '%' }">
                <div class="glow"></div>
            </div>
        </div>
    </transition>
</template>
<script setup>
import { ref, watch } from 'vue'
import { loadingState } from '@/store/loading'

const displayProgress = ref(0)
const shouldRender = ref(false)
let interval = null

watch(
    () => loadingState.active,
    (newVal) => {
        if (newVal) {
            shouldRender.value = true
            displayProgress.value = 0
            interval = setInterval(() => {
                if (displayProgress.value < 90) {
                    displayProgress.value += Math.random() * 5
                }
            }, 200)
        } else {
            if (interval) clearInterval(interval)
            displayProgress.value = 100
            setTimeout(() => {
                shouldRender.value = false
            }, 400)
        }
    },
)
</script>

<style scoped>
.loading-bar-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    z-index: 99999;
}

.progress-bar {
    height: 100%;
    background: #ff0000;
    transition: width 0.4s cubic-bezier(0.1, 0.05, 0.36, 1);
    position: relative;
}

.glow {
    position: absolute;
    right: 0;
    width: 100px;
    height: 100%;
    box-shadow:
        0 0 10px #ff0000,
        0 0 5px #ff0000;
    opacity: 1;
    transform: rotate(3deg) translate(0px, -4px);
}

/* Hiệu ứng ẩn hiện mượt mà */
.fade-leave-active {
    transition: opacity 0.4s ease;
}
.fade-leave-to {
    opacity: 0;
}
</style>
