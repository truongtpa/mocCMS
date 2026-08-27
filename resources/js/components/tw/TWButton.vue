<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center gap-1.5 font-bold rounded-lg transition-all',
            'focus:outline-none focus:ring-2 focus:ring-offset-1',
            sizeClasses[size],
            variantClasses[variant],
            disabled ? 'opacity-50 cursor-not-allowed' : 'shadow-sm active:scale-[0.98]'
        ]"
        @click="$emit('click', $event)"
    >
        <slot name="icon"></slot>
        <slot></slot>
        <slot name="icon-right"></slot>
    </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'success', 'warning', 'ghost', 'white'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])

const sizeClasses = {
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2 text-sm',
    lg: 'px-5 py-2.5 text-base',
}

const variantClasses = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    secondary: 'bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 focus:ring-slate-500',
    danger: 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500',
    success: 'bg-emerald-500 text-white hover:bg-emerald-600 focus:ring-emerald-500',
    warning: 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-500',
    ghost: 'bg-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100 shadow-none',
    white: 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 focus:ring-slate-200',
}
</script>
