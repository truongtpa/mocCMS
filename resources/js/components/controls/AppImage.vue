<template>
    <span :class="['app-image', `app-image-${status}`]" :style="boxStyle">
        <img
            v-if="status !== 'error'"
            :src="currentSrc"
            :alt="alt"
            :width="width || null"
            :height="height || null"
            :loading="lazy ? 'lazy' : 'eager'"
            :style="{ objectFit: fit }"
            decoding="async"
            class="app-image-img"
            @load="status = 'loaded'"
            @error="onError"
        >
        <span v-else class="app-image-empty" :title="alt">
            <i :class="['bi', icon]" aria-hidden="true"></i>
        </span>
    </span>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

/**
 * AppImage — ảnh tải trễ, hỏng link thì hiện ảnh mặc định.
 *
 * Thứ tự rơi về: src -> fallback (nếu truyền) -> ô icon.
 *
 *   <AppImage :src="item.anh_dai_dien" alt="Ảnh bìa" width="120" height="80" />
 *   <AppImage :src="user.anh" fallback="/asset/img/user.png" class="rounded-circle" />
 */
const props = defineProps({
    src: { type: String, default: '' },
    alt: { type: String, default: '' },

    /** Ảnh dùng khi src lỗi; bỏ trống thì hiện ô icon */
    fallback: { type: String, default: '' },
    icon: { type: String, default: 'bi-image' },

    width: { type: [String, Number], default: '' },
    height: { type: [String, Number], default: '' },
    fit: { type: String, default: 'cover' },
    lazy: { type: Boolean, default: true },
})

const currentSrc = ref(props.src)
const status = ref(props.src ? 'loading' : 'error')

watch(
    () => props.src,
    (src) => {
        currentSrc.value = src
        status.value = src ? 'loading' : 'error'
    }
)

const toCss = (v) => (v === '' || v == null ? null : typeof v === 'number' || /^\d+$/.test(v) ? `${v}px` : v)

const boxStyle = computed(() => ({ width: toCss(props.width), height: toCss(props.height) }))

// Ảnh mặc định cũng hỏng thì thôi, chuyển hẳn sang ô icon
function onError() {
    if (props.fallback && currentSrc.value !== props.fallback) {
        currentSrc.value = props.fallback
        status.value = 'loading'
        return
    }
    status.value = 'error'
}
</script>

<style scoped>
.app-image {
    position: relative;
    display: inline-block;
    overflow: hidden;
    vertical-align: middle;
    background-color: var(--app-surface-sunken);
    border-radius: inherit;
}

.app-image-img {
    display: block;
    width: 100%;
    height: 100%;
}

/* Chưa tải xong thì giấu ảnh để không thấy cảnh vẽ dở trên nền chờ */
.app-image-loading .app-image-img {
    opacity: 0;
}

.app-image-loading {
    animation: app-image-pulse 1.2s ease-in-out infinite;
}

.app-image-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    min-width: 1.5rem;
    min-height: 1.5rem;
    font-size: 1.25rem;
    color: var(--app-text-subtle);
}

@keyframes app-image-pulse {
    50% {
        background-color: var(--app-gray-200);
    }
}

@media (prefers-reduced-motion: reduce) {
    .app-image-loading {
        animation: none;
    }
}
</style>
