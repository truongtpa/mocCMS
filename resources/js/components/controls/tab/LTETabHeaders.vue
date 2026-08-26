<template>
    <div class="tab-headers mt-1 d-flex card-title-small">
        <button v-for="tab in tabs" :key="tab.key" :class="{ active: tab.key === activeTab }"
            class="tab border-right gap-1 w-100" @click="$emit('select', tab.key)">
            <i v-if="tab.icon" :class="tab.icon"></i>
            <span v-html="tab.name || 'Untitled'"></span>
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
}

.tab-headers:has(.tab:hover:not(.active)) .tab.active::before {
    width: 0%;
    left: 50%;
}

.tab {
    background-color: rgb(232, 232, 232);
    border: none;
    border-radius: 5px 5px 0 0;
    position: relative;
    transition: box-shadow 0.3s ease, transform 0.3s ease, background-color 0.15s ease;
    padding: 4px 16px 6px 16px;
}

.tab.active {
    background-color: white;
    box-shadow: -3px -5px 3px rgba(211, 211, 211, 0.5),
        3px -5px 3px rgba(211, 211, 211, 0.5);
    /* border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    border-top: 3px solid blue; */
    transition: box-shadow 0.3s ease, transform 0.3s ease, background-color 0.15s ease;
}

.tab::before {
    content: "";
    position: absolute;
    left: 50%;
    top: 0;
    /* Đặt border-top phía trên */
    width: 0%;
    height: 4px;
    background-color: blue;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    transition: width 0.3s ease-in-out, left 0.3s ease-in-out;
}

.tab.active::before {
    width: 100%;
    left: 0;
}

.tab:hover:not(.active) {
    /* background-color: #d9edf7; */
    box-shadow: -2px -2px 3px rgba(211, 211, 211, 0.5),
    2px -2px 3px rgba(211, 211, 211, 0.5) !important;
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
