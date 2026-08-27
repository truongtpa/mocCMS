<template>
    <div class="relative">
        <select
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :disabled="disabled"
            :class="[
                'block w-full appearance-none rounded-lg border border-slate-200 bg-white leading-tight text-slate-700 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20',
                sizeClasses[size],
                disabled ? 'bg-slate-50 cursor-not-allowed opacity-75' : 'hover:border-slate-300'
            ]"
        >
            <option v-if="placeholder" value="" disabled selected hidden>
                {{ placeholder }}
            </option>
            <option v-for="(opt, index) in normalizedOptions" :key="index" :value="opt.value">
                {{ opt.text }}
            </option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
            </svg>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        default: ''
    },
    options: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: ''
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

defineEmits(['update:modelValue'])

const sizeClasses = {
    sm: 'py-1.5 pl-2 pr-7 text-xs',
    md: 'py-2 pl-2.5 pr-8 text-sm',
    lg: 'py-2.5 pl-4 pr-10 text-base',
}

// Normalize options format: supports both ["A", "B"] and [{value: "A", text: "A"}, ...]
const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object' && opt !== null) {
            // Assume it has value and text/label
            return {
                value: opt.value !== undefined ? opt.value : opt.id,
                text: opt.text !== undefined ? opt.text : (opt.label || opt.name)
            }
        }
        return { value: opt, text: opt }
    })
})
</script>
