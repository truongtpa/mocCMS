<template>
    <span
        :class="[
            'app-badge',
            `app-badge-${variant}`,
            { 'font-weight-bold': bold }
        ]"
    >
        <slot></slot>
    </span>
</template>

<script setup>
defineProps({
    variant: {
        type: String,
        default: 'secondary',
        validator: (val) => ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'].includes(val)
    },
    bold: {
        type: Boolean,
        default: true
    }
})
</script>

<style scoped>
.app-badge {
    display: inline-block;
    font-size: 0.7rem !important;
    line-height: 1.2 !important;
    padding: 0.18rem 0.45rem !important;
    border-radius: 4px !important;
    vertical-align: middle;
}

.app-badge-primary {
    background-color: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
}

.app-badge-secondary {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
}

.app-badge-success {
    background-color: #28a745 !important;
    color: #ffffff !important;
}

.app-badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.app-badge-danger {
    background-color: #dc3545 !important;
    color: #ffffff !important;
}

.app-badge-info {
    background-color: #17a2b8 !important;
    color: #ffffff !important;
}

.app-badge-light {
    background-color: #f8fafc !important;
    color: #334155 !important;
    border: 1px solid #e2e8f0 !important;
}

.app-badge-dark {
    background-color: #1e293b !important;
    color: #ffffff !important;
}
</style>
