<template>
    <div class="form-group form-group-sm" :class="class" v-if="visible">
        <label v-if="visibleLabel && label">{{ label }}</label>
        <input
            :placeholder="placeholder"
            :type="type"
            :class="[`${useUI.isMobile() ? 'form-control-sm' : ''}`, inputClass]"
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
            default: 'form-control',
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
        extraClasses() {
            const classes = []

            if (this.class) {
                classes.push(this.class)
            }

            if (this.windowWidth < 768) {
                classes.push('btn-sm')
            }

            return classes
        },
    },
    setup() {
        const useUI = useUIManager()
        return {
            useUI,
        }
    },
}
</script>
