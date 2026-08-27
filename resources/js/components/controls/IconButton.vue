<template>
    <button
        :type="type"
        :disabled="disabled"
        :title="title"
        :class="[
            'btn-icon-pure',
            `btn-icon-pure--${variant}`,
            { 'btn-icon-pure--disabled': disabled }
        ]"
        @click="$emit('click', $event)"
    >
        <slot>
            <i v-if="icon" :class="icon"></i>
        </slot>
    </button>
</template>

<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'slate',
        validator: (value) => [
            'blue', 'indigo', 'emerald', 'amber', 'red', 'slate',
            'primary', 'success', 'warning', 'danger'
        ].includes(value)
    },
    title: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])
</script>

<style scoped>
.btn-icon-pure {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 4px;
    border-radius: 6px;
    border: 1px solid transparent;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background-color: #f1f5f9;
    color: #475569;
    flex-shrink: 0;
    margin: 0 0px;
}
.btn-icon-pure i {
    font-size: 12px;
}

.btn-icon-pure:active:not(.btn-icon-pure--disabled) {
    transform: scale(0.95);
}

.btn-icon-pure--disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* Primary/Blue */
.btn-icon-pure--blue,
.btn-icon-pure--primary {
    background-color: #eff6ff;
    color: #2563eb;
    border-color: #dbeafe;
}
.btn-icon-pure--blue:hover:not(.btn-icon-pure--disabled),
.btn-icon-pure--primary:hover:not(.btn-icon-pure--disabled) {
    background-color: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.2);
}

/* Indigo */
.btn-icon-pure--indigo {
    background-color: #eef2ff;
    color: #4f46e5;
    border-color: #e0e7ff;
}
.btn-icon-pure--indigo:hover:not(.btn-icon-pure--disabled) {
    background-color: #4f46e5;
    color: #ffffff;
    border-color: #4f46e5;
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2), 0 2px 4px -2px rgba(79, 70, 229, 0.2);
}

/* Success/Emerald */
.btn-icon-pure--emerald,
.btn-icon-pure--success {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #d1fae5;
}
.btn-icon-pure--emerald:hover:not(.btn-icon-pure--disabled),
.btn-icon-pure--success:hover:not(.btn-icon-pure--disabled) {
    background-color: #059669;
    color: #ffffff;
    border-color: #059669;
    box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.2), 0 2px 4px -2px rgba(5, 150, 105, 0.2);
}

/* Warning/Amber */
.btn-icon-pure--amber,
.btn-icon-pure--warning {
    background-color: #fffbeb;
    color: #d97706;
    border-color: #fef3c7;
}
.btn-icon-pure--amber:hover:not(.btn-icon-pure--disabled),
.btn-icon-pure--warning:hover:not(.btn-icon-pure--disabled) {
    background-color: #d97706;
    color: #ffffff;
    border-color: #d97706;
    box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.2), 0 2px 4px -2px rgba(217, 119, 6, 0.2);
}

/* Danger/Red */
.btn-icon-pure--red,
.btn-icon-pure--danger {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: #fee2e2;
}
.btn-icon-pure--red:hover:not(.btn-icon-pure--disabled),
.btn-icon-pure--danger:hover:not(.btn-icon-pure--disabled) {
    background-color: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2), 0 2px 4px -2px rgba(220, 38, 38, 0.2);
}

/* Slate/Gray */
.btn-icon-pure--slate {
    background-color: #f8fafc;
    color: #475569;
    border-color: #e2e8f0;
}
.btn-icon-pure--slate:hover:not(.btn-icon-pure--disabled) {
    background-color: #475569;
    color: #ffffff;
    border-color: #475569;
    box-shadow: 0 4px 6px -1px rgba(71, 85, 105, 0.2), 0 2px 4px -2px rgba(71, 85, 105, 0.2);
}
</style>
