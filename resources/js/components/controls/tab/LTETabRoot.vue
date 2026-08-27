<template>
    <div class="tab-root rounded" :class="props.class">
        <LTETabHeaders :tabs="tabs" :activeTab="activeTab" @select="selectTab" />
        <div>
            <slot />
        </div>
    </div>
</template>

<script setup>
import { ref, provide } from 'vue'
import LTETabHeaders from './LTETabHeaders.vue'

const props = defineProps({
    tabs: {
        type: Array,
        required: true
    },
    class: {
        type: String,
        default: ""
    }
})

const activeTab = ref(props.tabs[0]?.key || '')

function selectTab(key) {
    activeTab.value = key
}

// Cung cấp activeTab xuống TabContent
provide('activeTab', activeTab)
</script>

<style>
.tab-root {
    background-color: white;
}
</style>
