<template>
    <div v-if="visible" class="overlay">
        <div class="loader"></div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
    visible: {
        type: Boolean,
        default: false,
    },
})
</script>

<style scoped>
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.loader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotateX(65deg) rotate(45deg);
    width: 80px;
    height: 80px;
    background: var(--primary);
    animation: layers1 1s linear infinite alternate;
    z-index: 1000;
}

.loader:after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.7);
    animation: layerTr 0.5s linear infinite alternate;
}

/* Animation */
@keyframes layers1 {
    0% { box-shadow: 0px 0px 0 0px; }
    90%, 100% { box-shadow: 30px 30px 0 -4px; }
}
@keyframes layerTr {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(-35px, -35px) scale(1); }
}
</style>
