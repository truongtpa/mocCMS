<template>
    <div class="dropdown btn-group">
        <button
            type="button"
            :class="[`btn dropdown-toggle dropdown-icon ${useUI.isMobile() ? 'btn-sm' : ''}`, buttonClass]"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            :disabled="disabled"
        >
            <i v-if="icon" :class="[icon, 'mr-1']"></i>
            <span>{{ text }}</span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>

        <div class="dropdown-menu dropdown-menu-auto" role="menu">
            <template v-for="(item, index) in items" :key="index">
                <div v-if="item.divider" class="dropdown-divider"></div>

                <button v-else type="button" class="dropdown-item d-flex align-items-center" @click="item.action">
                    <i v-if="item.icon" :class="[item.icon, 'item-icon']"></i>
                    <span class="item-label">{{ item.label }}</span>
                </button>
            </template>
        </div>
    </div>
</template>

<script setup>
import { useUIManager } from '@/store/ui_manager'

defineProps({
    text: { type: String, default: 'Thao tác' },
    icon: { type: String, default: '' },
    buttonClass: { type: String, default: 'btn-primary' },
    disabled: { type: Boolean, default: false },
    items: {
        type: Array,
        required: true,
        default: () => [],
    },
})

const useUI = useUIManager()
</script>

<style scoped>
.dropdown-menu {
    border-radius: 0.375rem !important;
    padding: 0.25rem !important;
    margin-top: 0.25rem !important;
    min-width: 12.5rem;
    background-color: #ffffff;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
}

.dropdown-item {
    cursor: pointer;
    padding: 0.5rem 0.75rem !important;
    border-radius: 0.25rem !important;
    font-size: 0.875rem;
    font-weight: 400;
    color: #334155;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.dropdown-item:hover {
    background-color: #f1f5f9 !important;
    color: #0284c7 !important;
}

.item-icon {
    width: 18px;
    margin-right: 0.5rem;
    color: #0284c7;
    font-size: 0.9rem;
    text-align: center;
}

.dropdown-divider {
    margin: 0.25rem 0 !important;
    border-top: 1px solid #e2e8f0 !important;
}
</style>
