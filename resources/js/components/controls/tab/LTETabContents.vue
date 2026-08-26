<template>
    <div class="tab-content" v-show="tabkey === activeTab">
        <slot />
    </div>
</template>

<script setup>
import {inject, onMounted} from 'vue'

const props = defineProps({
    tabkey: {
        type: String,
        required: true
    }
})

const activeTab = inject('activeTab')
</script>
<style>
.tab-content {
    z-index: 3;
    position: relative;
    background-color: white;
}
</style>
