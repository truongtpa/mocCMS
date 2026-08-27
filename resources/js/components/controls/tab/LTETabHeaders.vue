<template>
    <div class="tab-headers mt-1 d-flex card-title-small">
        <button v-for="tab in tabs" :key="tab.key" :class="{ active: tab.key === activeTab }"
            class="tab border-right gap-1" @click="$emit('select', tab.key)">
            <i v-if="tab.icon" :class="tab.icon"></i>
            <span class="ml-2">{{ tab.name || 'Untitled' }}</span>
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
.tab-headers:has(.tab:hover:not(.active)) .tab.active {
    box-shadow: none;
    opacity: 0.6;
}

.tab-headers:has(.tab:hover:not(.active)) .tab.active::before {
    width: 30%;
    left: 35%;
}

.tab {
    background-color: transparent;
    border: none;
    position: relative;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    padding: 4px 16px 6px 16px;
}

.tab.active {
    /* background-color: #d9edf7; */
    box-shadow: -5px 3px 10px rgba(0, 0, 0, 0.15),
        5px 3px 10px rgba(0, 0, 0, 0.15);
    /* border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    border-top: 3px solid blue; */
}

.tab::before {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 100%;
    /* Đặt border-top phía trên */
    width: 0%;
    height: 4px;
    background-color: blue;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    transition: width 0.3s ease-in-out, left 0.3s ease-in-out;
}

.tab.active::before {
    width: 100%;
    left: 0;
}

.tab:hover:not(.active) {
    /* background-color: #d9edf7; */
    box-shadow: -5px 3px 10px rgba(0, 0, 0, 0.15),
        5px 3px 10px rgba(0, 0, 0, 0.15) !important;
    /* border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    border-top: 3px solid blue; */
    z-index: 2;
    position: relative;
}

.tab:hover:not(.active)::before {
    width: 100%;
    left: 0;
}

@media (max-width: 991.98px) {
    .tab span {
        display: none;
    }
}
</style>
