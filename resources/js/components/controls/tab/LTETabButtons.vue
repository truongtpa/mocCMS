<template>
    <div class="tab-headers mt-1 mb-3 card-title-small">
        <button v-for="tab in tabs" :key="tab.key" :class="{ active: tab.key === activeTab }"
                class="btn btn-primary mr-2" @click="$emit('select', tab.key)">
            <i v-if="tab.icon" :class="tab.icon" class="mr-2" ></i>
            <span>{{ tab.name || 'Untitled' }}</span>
        </button>
    </div>
</template>

<script setup>
defineProps({
    tabs: Array,
    activeTab: String
})
defineEmits(['select'])
</script>

<style scoped>

@media (max-width: 991.98px) {
    .tab span {
        display: none;
    }
}
</style>
