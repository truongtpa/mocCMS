<template>
    <div v-if="!removed" :class="cardClass">
        <div v-if="hasHeader" :class="['card-header', headerClass]">
            <slot name="header">
                <h3 class="card-title">
                    <slot name="title">
                        <i v-if="icon" :class="['bi', icon, 'me-1']" aria-hidden="true"></i>{{ title }}
                    </slot>
                </h3>
                <div class="card-tools">
                    <slot name="tools" />
                    <button
                        v-if="collapsible"
                        type="button"
                        class="btn btn-tool"
                        :title="collapsed ? 'Mở rộng' : 'Thu gọn'"
                        @click="collapsed = !collapsed"
                    >
                        <i :class="['bi', collapsed ? 'bi-plus-lg' : 'bi-dash-lg']" aria-hidden="true"></i>
                    </button>
                    <button
                        v-if="removable"
                        type="button"
                        class="btn btn-tool"
                        title="Đóng"
                        @click="removed = true"
                    >
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </slot>
        </div>

        <Transition v-bind="transition">
            <div v-show="!collapsed">
                <div :class="['card-body', bodyClass]">
                    <slot />
                </div>
                <div v-if="$slots.footer" :class="['card-footer', footerClass]">
                    <slot name="footer" />
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { computed, ref, useSlots } from 'vue'
import { collapseTransition } from './collapseTransition'

/**
 * Khung nội dung có tiêu đề. Thay cho <LteCard>.
 *
 * Phần nhìn (viền, bo góc, header chìm) do resources/css/theme.css quyết định
 * trên class `.card` của Bootstrap — ở đây chỉ lo cấu trúc và hành vi.
 */
const props = defineProps({
    title: { type: String, default: '' },
    icon: { type: String, default: '' },

    /** Tên màu Bootstrap: primary, success, danger… để lên viền trên của card */
    theme: { type: String, default: '' },

    collapsible: { type: Boolean, default: false },
    defaultCollapsed: { type: Boolean, default: false },
    removable: { type: Boolean, default: false },

    headerClass: { type: String, default: '' },
    bodyClass: { type: String, default: '' },
    footerClass: { type: String, default: '' },
})

const slots = useSlots()
const transition = collapseTransition()

const collapsed = ref(props.defaultCollapsed)
const removed = ref(false)

const hasHeader = computed(
    () => !!(props.title || slots.header || slots.title || slots.tools || props.collapsible || props.removable)
)

const cardClass = computed(() => [
    'card',
    props.theme && `card-accent-${props.theme}`,
    collapsed.value && 'collapsed-card',
])
</script>
