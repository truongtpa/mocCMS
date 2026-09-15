<template>
    <div v-if="hasHeader" class="app-content-header">
        <div :class="containerClass">
            <div class="row">
                <div class="col-sm-6">
                    <slot name="header">
                        <h3 v-if="title">{{ title }}</h3>
                    </slot>
                </div>
                <div v-if="breadcrumbs.length" class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li
                            v-for="(crumb, i) in breadcrumbs"
                            :key="i"
                            :class="['breadcrumb-item', { active: i === breadcrumbs.length - 1 }]"
                        >
                            <a v-if="crumb.href" :href="crumb.href">{{ crumb.label }}</a>
                            <template v-else>{{ crumb.label }}</template>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div :class="containerClass">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { computed, useSlots } from 'vue'

/**
 * Thân một trang: dải tiêu đề + vùng nội dung.
 *
 * Thay cho <LteAppContent>. Không có tiêu đề, breadcrumb hay slot #header thì
 * dải trên được bỏ hẳn, nội dung sát lên đầu.
 */
const props = defineProps({
    title: { type: String, default: '' },

    /** [{ label, href? }] — phần tử cuối tự động ở trạng thái active */
    breadcrumbs: { type: Array, default: () => [] },

    /** false = giới hạn bề ngang theo container-lg thay vì tràn hết màn */
    fluid: { type: Boolean, default: true },
})

const slots = useSlots()

const containerClass = computed(() => (props.fluid ? 'container-fluid' : 'container-lg'))
const hasHeader = computed(() => !!(props.title || props.breadcrumbs.length || slots.header))
</script>
