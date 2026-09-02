<template>
    <div :class="[hasLabel ? 'form-group mb-2' : 'm-0 p-0', extraClasses]" v-if="visible">
        <label v-if="hasLabel" :class="labelClass">{{ label }}</label>
        <input
            :placeholder="placeholder"
            :type="type"
            :class="[inputClass]"
            :id="inputId"
            :value="modelValue"
            :disabled="isDisabled"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>

<script>
import { useUIManager } from '@/store/ui_manager'
export default {
    props: {
        label: {
            type: String,
            default: '',
        },
        labelClass: {
            type: String,
            default: 'font-weight-bold small text-muted text-xs mb-1',
        },
        placeholder: {
            type: String,
            default: '',
        },
        type: {
            type: String,
            default: 'text',
        },
        inputClass: {
            type: String,
            default: 'form-control form-control-sm text-xs',
        },
        class: {
            type: String,
            default: '',
        },
        inputId: {
            type: String,
            default: '',
        },
        modelValue: {
            type: [String, Number],
            default: '',
        },
        isDisabled: {
            type: Boolean,
            default: false,
        },
        visible: {
            type: Boolean,
            default: true,
        },
        visibleLabel: {
            type: Boolean,
            default: true,
        },
    },
    computed: {
        hasLabel() {
            return this.visibleLabel && this.label && String(this.label).trim() !== '';
        },
        extraClasses() {
            return this.class || '';
        }
    },
    setup() {
        const useUI = useUIManager()
        return {
            useUI,
        }
    },
}
</script>
